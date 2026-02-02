
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
import { TrendsData } from '@/types/trends';
import PlatformSection from '@/Components/PlatformSection';
import { useEffect } from 'react';

interface Props {
    trends: TrendsData;
    last_update: string;
}

export default function Dashboard({ trends, last_update }: Props) {

    // Auto refresh every 60 seconds
    useEffect(() => {
        const interval = setInterval(() => {
            router.reload({ only: ['trends', 'last_update'] });
        }, 60000);
        return () => clearInterval(interval);
    }, []);

    const refreshNow = () => {
        router.reload({ only: ['trends', 'last_update'] });
    }

    return (
        <AuthenticatedLayout
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Trends Monitor</h2>}
        >
            <Head title="Real-time Trends" />

            <div className="py-6 h-screen overflow-hidden flex flex-col">
                <div className="max-w-[1920px] w-full mx-auto sm:px-4 lg:px-6 h-full">
                    <div className="mb-4 flex justify-between items-center px-2">
                        <span className="text-xs text-gray-500">Last updated: {last_update}</span>
                        <button onClick={refreshNow} className="px-3 py-1 bg-indigo-600 text-white rounded text-xs hover:bg-indigo-700">
                            Refresh
                        </button>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 h-[calc(100%-80px)]">
                        {/* TikTok */}
                        <PlatformSection
                            title="TikTok"
                            platform="tiktok"
                            icon={<span>🎵</span>}
                            color="#000000"
                            trends={trends.tiktok || []}
                        />

                        {/* Instagram */}
                        <PlatformSection
                            title="Instagram"
                            platform="instagram"
                            icon={<span>📸</span>}
                            color="#E1306C"
                            trends={trends.instagram || []}
                        />

                        {/* YouTube */}
                        <PlatformSection
                            title="YouTube"
                            platform="youtube"
                            icon={<span>📺</span>}
                            color="#FF0000"
                            trends={trends.youtube || []}
                        />

                        {/* Google */}
                        <PlatformSection
                            title="Google"
                            platform="google"
                            icon={<span>🔍</span>}
                            color="#4285F4"
                            trends={trends.google || []}
                        />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
