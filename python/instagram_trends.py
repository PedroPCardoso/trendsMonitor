
import sys
import json
import logging
import time
from playwright.sync_api import sync_playwright

# Setup logging
logging.basicConfig(level=logging.ERROR)
logger = logging.getLogger(__name__)

def fetch_instagram_trends(hashtags):
    trends = []
    
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(
             user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
        )
        page = context.new_page()
        
        for index, tag in enumerate(hashtags):
            try:
                url = f"https://www.instagram.com/explore/tags/{tag}/"
                logger.info(f"Navigating to {url}")
                page.goto(url, wait_until="networkidle", timeout=15000)
                
                # Check for login redirection
                if "login" in page.url:
                    logger.warning(f"Redirected to login for #{tag}")
                    # In a real scenario, we might need cookies. 
                    # For now, we skip if login wall hits.
                    continue

                # Extract post count (often in meta description or header)
                # Strategy: Search for "X posts" text or meta tag
                
                post_count = 0
                time.sleep(2) # brief wait for rendering
                
                # Meta description strategy usually works best for non-logged in
                try:
                    meta_desc = page.locator('meta[name="description"]').get_attribute('content')
                    if meta_desc:
                        # Format: "82M posts - Discover photos and videos..."
                        parts = meta_desc.split(' posts')
                        if len(parts) > 1:
                            count_str = parts[0].strip().replace(',', '').replace('.', '')
                            # Handle K/M suffix logic here if needed, but keeping it simple string for now
                            trends.append({
                                "hashtag": tag,
                                "rank": index + 1,
                                "metadata": {
                                    "posts_summary": count_str
                                }
                            })
                            continue
                except:
                    pass
                
                # Fallback: Just assume it exists if we are here
                trends.append({
                    "hashtag": tag,
                    "rank": index + 1,
                    "metadata": {
                        "posts_summary": "Active"
                    }
                })

            except Exception as e:
                logger.error(f"Error scraping Instagram #{tag}: {str(e)}")
                continue
        
        browser.close()
            
    print(json.dumps(trends))
        
if __name__ == "__main__":
    # Expect hashtags as comma separated string
    tags_arg = sys.argv[1].split(',') if len(sys.argv) > 1 else ['explorar', 'viral']
    fetch_instagram_trends(hashtags=tags_arg)
