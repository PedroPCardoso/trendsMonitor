import sys
import json
import logging
import time
import re
from playwright.sync_api import sync_playwright

# Setup logging
logging.basicConfig(level=logging.ERROR)
logger = logging.getLogger(__name__)

def scrape_tiktok_creative_center(region='BR'):
    """
    Scrapes trending hashtags from TikTok Creative Center.
    This is the official source for TikTok trending content.
    """
    trends = []
    
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(
            user_agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
            viewport={'width': 1920, 'height': 1080}
        )
        page = context.new_page()
        
        try:
            # Navigate to TikTok Creative Center - Trending Hashtags
            url = "https://ads.tiktok.com/business/creativecenter/inspiration/popular/hashtag/pc/en"
            sys.stderr.write(f"DEBUG: Navigating to TikTok Creative Center...\n")
            page.goto(url, timeout=60000, wait_until="domcontentloaded")
            
            # Wait for content to load - Use a more generic wait first
            sys.stderr.write("DEBUG: Waiting for page load...\n")
            try:
                page.wait_for_selector('body', timeout=10000)
                # Try to wait for the specific region selector, but don't fail if it takes too long
                try:
                    page.wait_for_selector('div[class*="TrendBanner_bannerRegionsSelect"]', timeout=10000)
                except Exception:
                    sys.stderr.write("DEBUG: Region selector wait timed out, proceeding anyway...\n")
                    
                time.sleep(5) # Extra wait for dynamic content
            except Exception as e:
                sys.stderr.write(f"DEBUG: Initial load warning: {e}\n")
            
            # 1. Select Region (Brazil)
            try:
                # Use a more generic selector if the specific one fails
                region_label = page.locator('[class*="TrendBanner_bannerRegionsSelectLabel"]').first
                if region_label.is_visible():
                    current_region = region_label.text_content()
                    if "Brazil" not in current_region:
                        sys.stderr.write(f"DEBUG: Changing region from {current_region} to Brazil...\n")
                        region_label.click()
                        time.sleep(2)
                        
                        # Search/Select Brazil
                        # Use resilient filtering
                        brazil_option = page.locator('div[class*="RegionSelect_regionSelectItem"]').filter(has_text="Brazil").first
                        if brazil_option.is_visible():
                            brazil_option.click()
                        else:
                            # Try searching
                            input_el = page.locator('input[placeholder*="Search"]')
                            if input_el.is_visible():
                                input_el.fill("Brazil")
                                time.sleep(1)
                                page.locator('div[class*="RegionSelect_regionSelectItem"]').filter(has_text="Brazil").first.click()
                        
                        time.sleep(5) # Wait for reload
                        sys.stderr.write("DEBUG: Region changed to Brazil\n")
            except Exception as e:
                sys.stderr.write(f"DEBUG: Region selection warning: {e}\n")

            # 2. Select Time Period (Last 7 days)
            try:
                # Find the period selector (usually "Yesterday" or "Last 7 days")
                period_selector = page.locator('.CcSelect_ccSelectValueWrap__r4_tq')
                # Wait a small bit to ensure it is interactive
                time.sleep(1)
                
                if period_selector.is_visible():
                    current_period = period_selector.text_content()
                    # We prefer "Last 7 days" or "Yesterday"
                    target_period = "Last 7 days"
                    
                    if target_period not in current_period:
                        sys.stderr.write(f"DEBUG: Changing period from {current_period} to {target_period}...\n")
                        period_selector.click()
                        time.sleep(1)
                        
                        # Click "Last 7 days" option
                        period_option = page.locator('div[class*="CcSelect_ccSelectItem"]').filter(has_text=target_period).first
                        if period_option.is_visible():
                            period_option.click()
                            time.sleep(3) # Wait for reload
                            sys.stderr.write(f"DEBUG: Period changed to {target_period}\n")
            except Exception as e:
                sys.stderr.write(f"DEBUG: Time period selection warning: {e}\n")

            # 3. Extract Hashtags
            # Using the specific selectors found in research
            sys.stderr.write("DEBUG: Extracting hashtags...\n")
            
            # Cards container selector based on research
            # a.CardPc_container___oNb0
            try:
                page.wait_for_selector('a[class*="CardPc_container"]', timeout=10000)
            except:
                pass
                
            cards = page.locator('a[class*="CardPc_container"]').all()
            
            sys.stderr.write(f"DEBUG: Found {len(cards)} hashtag cards\n")
            
            for i, card in enumerate(cards[:20]):
                try:
                    # Extract Hashtag Name
                    # div.CardPc_hashtagName__YDP_ is the specific class
                    name_el = card.locator('div[class*="CardPc_hashtagName"]').first
                    if not name_el.is_visible():
                         continue
                    
                    name = name_el.text_content().strip()
                    if not name: continue
                    
                    # Extract Rank
                    rank = i + 1
                    
                    # Extract Post Count (e.g. "767 Posts")
                    posts = "N/A"
                    # Get all text from card to parse metrics
                    card_text = card.text_content()
                    
                    # Look for number followed by "Posts"
                    # The number might have K/M suffix
                    metrics_match = re.search(r'(\d+(?:\.\d+)?[KMBkmb]?)\s*Posts?', card_text)
                    if metrics_match:
                        posts = metrics_match.group(1)
                    
                    # Extract Trend Change
                    change = "N/A"
                    if "NEW" in card_text:
                        change = "NEW"
                    else:
                        # Look for +number or -number (usually rank change)
                        change_match = re.search(r'([+\-]\d+)', card_text)
                        if change_match:
                            change = change_match.group(1)
                            
                    trends.append({
                        "hashtag": name if name.startswith('#') else f"#{name}",
                        "rank": rank,
                        "metadata": {
                            "posts": posts,
                            "trend_change": change,
                            "source": "creative_center_api",
                            "region": "Brazil",
                            "extracted_at": time.strftime("%Y-%m-%d %H:%M:%S")
                        }
                    })
                    
                except Exception as e:
                    sys.stderr.write(f"DEBUG: Error parsing card {i}: {e}\n")
                    continue
            
            # Fallback (in case selectors changed but content is there)
            if len(trends) < 5:
                sys.stderr.write("DEBUG: Few trends found via primary selector. Trying fallback text extraction...\n")
                content = page.content()
                tags = re.findall(r'#([a-zA-Z][a-zA-Z0-9_\u00C0-\u017F]+)', content)
                
                # Filter duplicates and bad tags
                unique_tags = []
                seen = set()
                invalid_terms = {'width', 'height', 'color', 'background', 'fff', '000', 'px', 'rem'}
                
                for t in tags:
                    curr = t.lower()
                    if curr not in seen and curr not in invalid_terms and len(curr) > 2:
                        # avoid hex colors
                        if re.match(r'^[0-9a-f]{3}$|^[0-9a-f]{6}$', curr):
                            continue
                            
                        seen.add(curr)
                        unique_tags.append(t)
                        if len(unique_tags) >= 10: break
                
                # Append only if not already found
                existing_names = {t['hashtag'].lower() for t in trends}
                for i, tag in enumerate(unique_tags):
                     tag_with_hash = f"#{tag}"
                     if tag_with_hash.lower() not in existing_names:
                        trends.append({
                            "hashtag": tag_with_hash,
                            "rank": len(trends) + 1,
                            "metadata": {"source": "creative_center_fallback", "region": "Brazil"}
                        })

        except Exception as e:
            logger.error(f"Error scraping TikTok Creative Center: {str(e)}")
            sys.stderr.write(f"DEBUG: Main error: {str(e)}\n")
            
        finally:
            browser.close()
    
    # Sort by rank
    trends.sort(key=lambda x: x['rank'])
    
    sys.stderr.write(f"DEBUG: Returning {len(trends)} trends\n")
    print(json.dumps(trends))

if __name__ == "__main__":
    scrape_tiktok_creative_center()
