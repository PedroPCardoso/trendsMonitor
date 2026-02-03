import React from 'react';
import { Trend } from '@/types/trends';

interface Props {
    trend: Trend;
    index: number;
    color: string;
    platform: string;
}

export default function TrendCard({ trend, index, color, platform }: Props) {
    const title = trend.title || trend.hashtag || 'Unknown';

    return (
        <div className="glass-card rounded-xl p-4 flex gap-4 items-start group hover:-translate-y-1 hover:shadow-lg hover:shadow-purple-500/10 cursor-default relative overflow-hidden">
            {/* Hover Glow Effect */}
            <div className={`absolute -inset-1 bg-gradient-to-r ${color} opacity-0 group-hover:opacity-10 transition duration-500 blur-lg`}></div>

            {/* Rank */}
            <div className={`font-mono text-xl font-bold bg-clip-text text-transparent bg-gradient-to-br ${color} w-8 shrink-0`}>
                #{trend.rank}
            </div>

            {/* Content */}
            <div className="flex-1 min-w-0 relative z-10">
                <h4 className="font-semibold text-gray-100 group-hover:text-white transition-colors line-clamp-2 leading-tight mb-1" title={title}>
                    {title}
                </h4>

                {/* Metadata */}
                <div className="flex flex-wrap gap-2 mt-2">
                    {/* YouTube Specific */}
                    {platform === 'youtube' && (
                        <>
                            {trend.metadata?.view_count && (
                                <span className="text-xs font-medium text-gray-400 bg-white/5 px-2 py-0.5 rounded border border-white/5 flex items-center gap-1">
                                    👁️ {typeof trend.metadata.view_count === 'number'
                                        ? new Intl.NumberFormat('en-US', { notation: "compact" }).format(trend.metadata.view_count)
                                        : trend.metadata.view_count}
                                </span>
                            )}
                            <span className="text-[10px] text-gray-500 border border-white/10 px-1.5 rounded truncate max-w-[100px]">
                                {trend.metadata?.channel_title}
                            </span>
                        </>
                    )}

                    {/* Instagram Specific */}
                    {platform === 'instagram' && (
                        <span className="text-xs font-medium text-gray-400 bg-white/5 px-2 py-0.5 rounded border border-white/5">
                            {trend.metadata.posts_summary || 'Trending'}
                        </span>
                    )}

                    {/* Google Specific */}
                    {platform === 'google' && (trend as any).entity_names?.slice(0, 2).map((entity: string, i: number) => (
                        <span key={i} className="text-[10px] uppercase font-bold tracking-wider text-gray-500 border border-white/10 px-1.5 rounded">
                            {entity}
                        </span>
                    ))}

                    {/* TikTok Specific */}
                    {platform === 'tiktok' && (
                        <span className="text-xs text-gray-500 italic">
                            {trend.metadata?.raw_text ? '🔥 Hot' : 'Trending'}
                        </span>
                    )}
                </div>
            </div>
        </div>
    );
}
