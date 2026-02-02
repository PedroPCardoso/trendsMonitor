
import sys
import json
import logging
from playwright.sync_api import sync_playwright

# Setup logging
logging.basicConfig(level=logging.ERROR)
logger = logging.getLogger(__name__)

def fetch_tiktok_trends(region='BR'):
    trends = []
    try:
        with sync_playwright() as p:
            # Launch browser
            browser = p.chromium.launch(headless=True)
            page = browser.new_page(
                user_agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
            )
            
            # URL for Creative Center (Hashtags)
            # Region parameter might vary, using query param approach
            url = f"https://ads.tiktok.com/business/creativecenter/inspiration/popular/hashtag/pc/en?region={region}"
            
            logger.info(f"Navigating to {url}")
            page.goto(url, wait_until="networkidle", timeout=30000)
            
            # Wait for the ranking list (Selectors need to be generic or specific enough)
            # Note: TikTok classes are often obfuscated. Looking for semantic structure or known common classes.
            # We wait for something that looks like a list item.
            
            # Attempt to locate generic trend cards / rows
            try:
                # Common pattern in Creative Center: list rows
                page.wait_for_selector('div[class*="RankingList_listContainer"]', timeout=10000)
            except:
                logger.warning("Timeout waiting for specific selector, trying to extract whatever available")

            # Extract data
            # This is fragile due to class name obfuscation. 
            # Strategy: Get all text content that looks like a rank and hashtag
            
            # Fallback selector strategy (simplified for stability in testing)
            # Real implementation would need constant maintenance of selectors
            rows = page.locator('div[class*="RankingList_rowContainer"]').all()
            
            # If empty, try searching for any list items with #
            if not rows:
                 logger.warning("RankingList_rowContainer not found, trying generic search")
                 
            for i, row in enumerate(rows):
                if i >= 10: break
                
                try:
                    # Extract rank (usually first number)
                    # Extract hashtag (starts with #)
                    text = row.inner_text()
                    lines = text.split('\n')
                    
                    hashtag = "Unknown"
                    for line in lines:
                        if line.startswith('#'):
                            hashtag = line
                            break
                            
                    # If we found a hashtag, looks good
                    if hashtag != "Unknown":
                        trends.append({
                            "hashtag": hashtag,
                            "rank": i + 1,
                            "metadata": {
                                "raw_text": text
                            }
                        })
                except Exception as e:
                    continue

            browser.close()
            
        print(json.dumps(trends))
        
    except Exception as e:
        logger.error(f"Error scraping TikTok: {str(e)}")
        print("[]")
        sys.exit(1)

if __name__ == "__main__":
    region_arg = sys.argv[1] if len(sys.argv) > 1 else 'BR'
    fetch_tiktok_trends(region=region_arg)
