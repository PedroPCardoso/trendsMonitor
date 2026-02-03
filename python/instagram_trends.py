import sys
import json
import logging
import time
import re
from collections import Counter
from playwright.sync_api import sync_playwright

# Setup logging
logging.basicConfig(level=logging.ERROR)
logger = logging.getLogger(__name__)

# Seed hashtags by category - Optimized for Brazil 2026
# These are entry points to discover trending content in specific niches
SEED_HASHTAGS = {
    "em_alta": [
        "viral", "explorar", "emalta", "bombando", "trend", 
        "reelsviral", "instagood", "fyp", "tbt"
    ],
    "atualidades": [
        "noticias", "brasil", "mundo", "fofoca", "choquei", 
        "celebridades", "famosos", "bbb26", "bbb", "redebbb"
    ],
    "entretenimento": [
        "memes", "memesbrasil", "humor", "engraçado", "comedia", 
        "standup", "videosengracados", "zueira"
    ],
    "lifestyle_vlog": [
        "lifestyle", "minhaarotina", "vlog", "dailyvlog", "vidareal", 
        "rotina", "donadecasa", "estudante", "trabalho"
    ],
    "moda_beleza": [
        "moda", "lookdodia", "fashion", "beleza", "makeup", 
        "maquiagem", "cabelo", "skincare", "outfit", "dicasdebeleza"
    ],
    "tech_inovacao": [
        "tecnologia", "inovacao", "ia", "ai", "apple", 
        "samsung", "xiaomi", "programacao", "dev", "tech"
    ],
    "esportes_saude": [
        "futebol", "brasileirao", "neymar", "flamengo", "corinthians",
        "palmeiras", "treino", "academia", "fitness", "saude"
    ],
    "musica_danca": [
        "musica", "lancamento", "funk", "sertanejo", "trap", 
        "dancinha", "coreografia", "show", "clipenovo"
    ],
    "sazonal": [
        "verao", "carnaval", "carnaval2026", "praia", "ferias", 
        "voltasasautlas", "verao2026"
    ]
}

def fetch_instagram_trends():
    """
    Scrapes Instagram to discover trending hashtags by:
    1. Visiting seed hashtag pages
    2. Extracting related hashtags from recent posts
    3. Ranking by frequency of appearance
    """
    all_found_hashtags = Counter()
    trends = []
    
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(
            user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
            viewport={'width': 1920, 'height': 1080}
        )
        page = context.new_page()
        
        # Block images to speed up scraping
        page.route("**/*.{png,jpg,jpeg,gif,webp}", lambda route: route.abort())
        
        visited_tags = set()
        
        # Select a subset of seeds from each category for efficiency
        seeds_to_visit = []
        for category, tags in SEED_HASHTAGS.items():
            seeds_to_visit.extend(tags[:3])  # Take 3 from each category
        
        sys.stderr.write(f"DEBUG: Will visit {len(seeds_to_visit)} seed hashtags\n")
        
        for tag in seeds_to_visit:
            if tag.lower() in visited_tags:
                continue
            visited_tags.add(tag.lower())
            
            try:
                url = f"https://www.instagram.com/explore/tags/{tag}/"
                sys.stderr.write(f"DEBUG: Visiting #{tag}...\n")
                page.goto(url, wait_until="domcontentloaded", timeout=15000)
                time.sleep(1.5)
                
                # Check if we hit a login wall
                if "login" in page.url.lower():
                    sys.stderr.write("DEBUG: Hit login wall, trying next tag\n")
                    continue
                
                # Extract page content
                page_content = page.content()
                
                # Extract all hashtags from the page
                # Improved Regex: Matches hashtags starting with a letter, length 3-30
                found_hashtags = re.findall(r'#([a-zA-Z][a-zA-Z0-9_\u00C0-\u017F]{2,29})', page_content)
                
                for hashtag in found_hashtags:
                    try:
                        tag_lower = hashtag.lower()
                        
                        # FILTER 1: Skip if it matches the seed exactly
                        if tag_lower == tag.lower():
                            continue
                            
                        # FILTER 2: CSS/Hex Color Codes and Dimensions
                        # Exclude 3 or 6 char hex codes (often picked up from styles)
                        if re.match(r'^[0-9a-f]{3}$|^[0-9a-f]{6}$', tag_lower):
                            continue
                            
                        # FILTER 3: Blocklist of common CSS/Web terms
                        blocklist = {
                            'width', 'height', 'color', 'background', 'style', 'fff', '000', 
                            'important', 'px', 'rem', 'em', 'auto', 'none', 'block', 'flex',
                            'margin', 'padding', 'border', 'font', 'rgb', 'rgba', 'var'
                        }
                        if tag_lower in blocklist:
                            continue
                            
                        # FILTER 4: Content Quality
                        # Must have at least 50% letters (avoid "2026", "12345" style tags unless mixed)
                        letter_count = sum(c.isalpha() for c in tag_lower)
                        if letter_count / len(tag_lower) < 0.5:
                            continue
                            
                        all_found_hashtags[tag_lower] += 1
                    except Exception:
                         continue
                
                # Try to extract post count from meta description
                try:
                    meta_desc = page.locator('meta[name="description"]').get_attribute('content')
                    if meta_desc:
                        sys.stderr.write(f"DEBUG: Meta for #{tag}: {meta_desc[:50]}...\n")
                except Exception:
                    pass
            except Exception as e:
                sys.stderr.write(f"DEBUG: Error scraping #{tag}: {e}\n")
                continue
        
        browser.close()
    
    # Get the most common hashtags (appearing across multiple seed pages)
    most_common = all_found_hashtags.most_common(20)
    
    sys.stderr.write(f"DEBUG: Found {len(all_found_hashtags)} unique hashtags\n")
    sys.stderr.write(f"DEBUG: Top hashtags: {most_common[:5]}\n")
    
    # If we found hashtags through scraping, use them
    if most_common:
        for i, (hashtag, count) in enumerate(most_common[:10]):
            trends.append({
                "hashtag": f"#{hashtag}",
                "rank": i + 1,
                "metadata": {
                    "frequency": count,
                    "source": "instagram_dynamic",
                    "scraped_at": time.strftime("%Y-%m-%d %H:%M:%S")
                }
            })
    else:
        # Fallback: return diverse seeds with category info
        sys.stderr.write("DEBUG: No dynamic hashtags found, using categorized seeds\n")
        trends = get_fallback_trends()
    
    print(json.dumps(trends))


def get_fallback_trends():
    """
    Returns a curated list of trending hashtags by category
    when dynamic scraping fails.
    """
    trends = []
    rank = 1
    
    # Return one top hashtag from each category
    for category, tags in SEED_HASHTAGS.items():
        if rank > 10:
            break
        trends.append({
            "hashtag": f"#{tags[0]}",
            "rank": rank,
            "metadata": {
                "category": category,
                "source": "curated_fallback"
            }
        })
        rank += 1
    
    return trends


if __name__ == "__main__":
    fetch_instagram_trends()
