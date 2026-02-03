import { PropsWithChildren } from 'react';
import { Head } from '@inertiajs/react';

export default function PublicLayout({ children }: PropsWithChildren) {
    return (
        <div className="min-h-screen relative font-sans text-gray-100 selection:bg-purple-500/30">
            {/* Background Effects */}
            <div className="mesh-gradient-bg" />
            <div className="fixed inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 pointer-events-none mix-blend-soft-light z-0"></div>

            {/* Navbar */}
            <nav className="fixed top-0 w-full z-50 border-b border-white/5 bg-[#030014]/80 backdrop-blur-xl">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16 items-center">
                        <div className="flex items-center gap-2">
                            <div className="w-8 h-8 rounded-lg bg-gradient-to-tr from-purple-600 to-pink-600 flex items-center justify-center shadow-lg shadow-purple-500/20">
                                <span className="font-bold text-white text-lg">T</span>
                            </div>
                            <span className="font-bold text-xl tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">
                                Trends<span className="text-purple-400">Monitor</span>
                            </span>
                        </div>
                        <div className="flex items-center gap-4">
                            <a href="https://github.com/PedroPCardoso/trendsMonitor" target="_blank" className="text-sm text-gray-400 hover:text-white transition-colors">
                                GitHub
                            </a>
                        </div>
                    </div>
                </div>
            </nav>

            {/* Main Content */}
            <main className="relative z-10 pt-24 pb-12">
                {children}
            </main>
        </div>
    );
}
