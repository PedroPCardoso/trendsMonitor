
import { Trend } from '@/types/trends';
import TrendCard from './TrendCard';

interface Props {
    title: string;
    icon: React.ReactNode;
    trends: Trend[];
    color: string;
    platform: string;
}

export default function PlatformSection({ title, icon, trends, color, platform }: Props) {
    return (
        <div className="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 border border-gray-200 dark:border-gray-800 h-full">
            <div className={`flex items-center gap-2 mb-4 pb-2 border-b ${color} border-opacity-20`} style={{ borderColor: color }}>
                <span className={`text-xl text-${color}-500`}>{icon}</span>
                <h2 className="text-lg font-bold text-gray-800 dark:text-gray-200">{title}</h2>
                <span className="ml-auto text-xs font-mono bg-gray-200 dark:bg-gray-800 px-2 py-1 rounded">
                    {trends.length}
                </span>
            </div>

            <div className="space-y-3 h-[600px] overflow-y-auto pr-2 scrollbar-thin">
                {trends.length === 0 ? (
                    <div className="text-center py-10 text-gray-400 text-sm">
                        No trends found.
                        <br />
                        Running workers...
                    </div>
                ) : (
                    trends.map((trend) => (
                        <TrendCard key={trend.rank} trend={trend} platform={platform} />
                    ))
                )}
            </div>
        </div>
    );
}
