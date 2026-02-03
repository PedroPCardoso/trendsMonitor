import { Head, router } from '@inertiajs/react';
import React, { useEffect, useState } from 'react';
import PublicLayout from '@/Layouts/PublicLayout';
import PlatformSection from '@/Components/PlatformSection';
import { TrendsData } from '@/types/trends';

interface Props {
    trends: TrendsData;
    last_update: string;
}

export default function Dashboard({ trends, last_update }: Props) {

    useEffect(() => {
        const interval = setInterval(() => {
            router.reload({ only: ['trends', 'last_update'] });
        }, 60000);
        return () => clearInterval(interval);
    }, []);

    const manualRefresh = () => {
        router.reload({ only: ['trends', 'last_update'] });
    }

    return (
        <PublicLayout>
            <Head title="Real-time Trends" />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* Hero Section */}
                <div className="text-center mb-16 relative">
                    <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[300px] bg-purple-500/20 blur-[100px] rounded-full -z-10"></div>
                    <h1 className="text-5xl md:text-7xl font-bold mb-6 tracking-tight">
                        <span className="bg-clip-text text-transparent bg-gradient-to-b from-white to-white/60">
                            Discover What's
                        </span>
                        <br />
                        <span className="bg-clip-text text-transparent bg-gradient-to-r from-purple-400 via-pink-400 to-red-400 glow-text">
                            Trending Now
                        </span>
                    </h1>
                    <p className="text-lg text-gray-400 max-w-2xl mx-auto mb-8">
                        Real-time insights from TikTok, Instagram, YouTube, and Google.
                        <br />Updated hourly to keep you ahead of the curve.
                    </p>

                    <div className="flex items-center justify-center gap-4 text-sm text-gray-500 font-mono">
                        <span className="flex items-center gap-2">
                            <span className="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            Live System
                        </span>
                        <span>•</span>
                        <span>Last update: {last_update}</span>
                        <button
                            onClick={manualRefresh}
                            className="ml-4 px-3 py-1 bg-white/5 hover:bg-white/10 border border-white/10 rounded-full transition-all text-xs text-white cursor-pointer"
                        >
                            Refresh
                        </button>
                    </div>
                </div>

                {/* Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <PlatformSection
                        platform="tiktok"
                        trends={trends.tiktok || []}
                        icon={<span className="text-2xl">🎵</span>}
                        color="from-cyan-400 to-blue-500"
                    />
                    <PlatformSection
                        platform="instagram"
                        trends={trends.instagram || []}
                        icon={<span className="text-2xl">📸</span>}
                        color="from-purple-500 to-pink-500"
                    />
                    <PlatformSection
                        platform="youtube"
                        trends={trends.youtube || []}
                        icon={<span className="text-2xl">📺</span>}
                        color="from-red-500 to-orange-500"
                    />
                    <PlatformSection
                        platform="google"
                        trends={trends.google || []}
                        icon={<span className="text-2xl">🔍</span>}
                        color="from-blue-400 to-green-400"
                    />
                </div>
            </div>
        </PublicLayout>
    );
}
