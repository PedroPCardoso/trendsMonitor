
import { Trend } from '@/types/trends';

interface Props {
    trend: Trend;
    platform: string;
}

export default function TrendCard({ trend, platform }: Props) {
    const title = trend.title || trend.hashtag || 'Unknown';

    return (
        <div className="flex items-start gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div className="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded-full font-bold text-gray-500">
                {trend.rank}
            </div>

            <div className="flex-1 min-w-0">
                <h4 className="font-semibold text-gray-900 dark:text-gray-100 truncate text-sm" title={title}>
                    {title}
                </h4>

                {trend.thumbnail_url && (
                    <img src={trend.thumbnail_url} alt={title} className="mt-2 w-full h-24 object-cover rounded-md" />
                )}

                <div className="mt-2 text-xs text-gray-500 space-y-1">
                    {platform === 'youtube' && (
                        <>
                            <p>Channel: {trend.metadata.channel_title}</p>
                            <p>{Number(trend.metadata.view_count).toLocaleString()} views</p>
                        </>
                    )}

                    {platform === 'tiktok' && (
                        <p>{trend.metadata?.raw_text || 'Active in Creative Center'}</p>
                    )}

                    {platform === 'google' && (
                        <p className="truncate">{trend.metadata.entity_names?.join(', ')}</p>
                    )}

                    {platform === 'instagram' && (
                        <p>{trend.metadata.posts_summary || 'Trending'}</p>
                    )}
                </div>
            </div>
        </div>
    );
}
