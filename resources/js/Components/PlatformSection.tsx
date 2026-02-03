import React from 'react';
import { Trend, PlatformName } from '@/types/trends';
import TrendCard from './TrendCard';

interface Props {
    platform: PlatformName;
    trends: Trend[];
    icon: React.ReactNode;
    color: string;
}

export default function PlatformSection({ platform, trends, icon, color }: Props) {
    const title = platform.charAt(0).toUpperCase() + platform.slice(1);

    return (
        <div className="glass-panel rounded-2xl p-1 relative overflow-hidden group h-full flex flex-col">
            <div className={`absolute top-0 left-0 w-full h-1 bg-gradient-to-r ${color}`}></div>

            <div className="p-5 flex-1 flex flex-col">
                <div className="flex items-center gap-3 mb-6">
                    <div className={`p-2 rounded-lg bg-gradient-to-br ${color} bg-opacity-10 text-white shadow-lg`}>
                        {icon}
                    </div>
                    <h3 className="text-lg font-bold text-white tracking-wide">{title}</h3>
                    <div className="ml-auto text-xs font-mono text-gray-500 bg-white/5 px-2 py-1 rounded">
                        {trends.length} items
                    </div>
                </div>

                <div className="space-y-3 flex-1 overflow-y-auto pr-2 scrollbar-thin">
                    {trends.length === 0 ? (
                        <div className="h-40 flex flex-col items-center justify-center text-center p-4 border border-dashed border-white/10 rounded-xl bg-white/5">
                            <div className="mb-2 w-5 h-5 border-2 border-white/20 border-t-purple-500 rounded-full animate-spin"></div>
                            <p className="text-gray-500 text-sm">Scanning trends...</p>
                        </div>
                    ) : (
                        trends.map((trend, index) => (
                            <TrendCard key={index} trend={trend} index={index} color={color} platform={platform} />
                        ))
                    )}
                </div>
            </div>
        </div>
    );
}
