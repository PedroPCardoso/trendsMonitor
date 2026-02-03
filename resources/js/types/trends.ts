
export interface Trend {
    rank: number;
    title?: string;
    hashtag?: string;
    description?: string;
    thumbnail_url?: string; // YouTube
    metadata: Record<string, any>;
}

export type PlatformName = 'youtube' | 'google' | 'tiktok' | 'instagram';

export interface TrendsData {
    youtube: Trend[];
    google: Trend[];
    tiktok: Trend[];
    instagram: Trend[];
}
