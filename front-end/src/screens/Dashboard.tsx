import React, { useState } from 'react';
import { Eye, EyeOff, ArrowUpRight, ArrowDownRight, QrCode, Wallet, Activity } from 'lucide-react';
import { Link } from 'react-router-dom';

export const Dashboard: React.FC = () => {
    const [showBalance, setShowBalance] = useState(true);

    return (
        <div className="flex flex-col min-h-screen bg-black pb-24 font-sans text-white">
            {/* Header / Top */}
            <div className="bg-gradient-to-b from-gray-900 to-black px-6 pt-12 pb-6 rounded-b-[40px] shadow-lg border-b border-gray-800">
                <div className="flex justify-between items-center mb-8">
                    <div className="flex items-center gap-3">
                        <div className="w-12 h-12 bg-teal-500 rounded-full flex items-center justify-center font-bold text-black text-xl shadow-lg shadow-teal-500/20 border-2 border-black">
                            A
                        </div>
                        <div>
                            <p className="text-gray-400 text-sm">Olá, bem-vindo!</p>
                            <h1 className="text-xl font-semibold tracking-wide">AG Soluções</h1>
                        </div>
                    </div>
                </div>

                {/* Balanço MOCK */}
                <div className="bg-gray-800/50 backdrop-blur-md rounded-3xl p-6 border border-gray-700/50 shadow-2xl relative overflow-hidden">
                    <div className="absolute top-0 right-0 w-32 h-32 bg-teal-500/10 rounded-full blur-3xl -mr-10 -mt-10"></div>

                    <div className="flex justify-between items-center mb-2">
                        <p className="text-gray-400 font-medium tracking-wider text-xs">SALDO DISPONÍVEL</p>
                        <button onClick={() => setShowBalance(!showBalance)} className="text-gray-400 hover:text-white transition-colors">
                            {showBalance ? <Eye size={20} /> : <EyeOff size={20} />}
                        </button>
                    </div>

                    <div className="flex items-baseline gap-2 mb-4">
                        <span className="text-2xl font-semibold text-gray-300">R$</span>
                        <span className={`text-4xl font-bold tracking-tight ${!showBalance ? 'blur-md opacity-70' : ''}`}>
                            {showBalance ? '14.230,50' : '******'}
                        </span>
                    </div>

                    <div className="flex items-center gap-2 text-sm text-teal-400 bg-teal-500/10 px-3 py-1.5 rounded-full w-fit">
                        <Activity size={16} />
                        <span className="font-medium">+R$ 1.150,00 hoje</span>
                    </div>
                </div>
            </div>

            {/* Quick Actions */}
            <div className="px-6 mt-6">
                <div className="grid grid-cols-3 gap-4">
                    <Link to="/receive" className="bg-gray-900 border border-gray-800 rounded-2xl p-4 flex flex-col items-center justify-center gap-3 hover:bg-gray-800 transition-colors group shadow-lg">
                        <div className="w-12 h-12 rounded-full bg-teal-500/10 text-teal-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <QrCode size={24} />
                        </div>
                        <span className="text-xs font-semibold text-gray-300">Cobrar</span>
                    </Link>

                    <div className="bg-gray-900 border border-gray-800 rounded-2xl p-4 flex flex-col items-center justify-center gap-3 hover:bg-gray-800 transition-colors group shadow-lg opacity-60">
                        <div className="w-12 h-12 rounded-full bg-blue-500/10 text-blue-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <ArrowUpRight size={24} />
                        </div>
                        <span className="text-xs font-semibold text-gray-300">Transferir</span>
                    </div>

                    <div className="bg-gray-900 border border-gray-800 rounded-2xl p-4 flex flex-col items-center justify-center gap-3 hover:bg-gray-800 transition-colors group shadow-lg opacity-60">
                        <div className="w-12 h-12 rounded-full bg-purple-500/10 text-purple-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <Wallet size={24} />
                        </div>
                        <span className="text-xs font-semibold text-gray-300">Cartões</span>
                    </div>
                </div>
            </div>

            {/* Ultimas Transações Recentes Widget */}
            <div className="px-6 mt-8">
                <div className="flex justify-between items-center mb-4">
                    <h2 className="text-lg font-semibold tracking-wide">Hoje</h2>
                    <Link to="/history" className="text-sm font-medium text-teal-400 hover:text-teal-300">Ver extrato</Link>
                </div>

                <div className="space-y-3">
                    {/* Mock Transaçao */}
                    <div className="flex items-center justify-between p-4 bg-gray-900 rounded-2xl border border-gray-800">
                        <div className="flex items-center gap-4">
                            <div className="w-10 h-10 rounded-full bg-green-500/10 text-green-500 flex items-center justify-center">
                                <ArrowDownRight size={20} />
                            </div>
                            <div>
                                <p className="font-medium text-white text-sm">Pix Recebido</p>
                                <p className="text-xs text-gray-500">João da Silva • 10:42</p>
                            </div>
                        </div>
                        <span className="font-semibold text-green-400 text-sm">+ R$ 85,00</span>
                    </div>

                    <div className="flex items-center justify-between p-4 bg-gray-900 rounded-2xl border border-gray-800">
                        <div className="flex items-center gap-4">
                            <div className="w-10 h-10 rounded-full bg-green-500/10 text-green-500 flex items-center justify-center">
                                <ArrowDownRight size={20} />
                            </div>
                            <div>
                                <p className="font-medium text-white text-sm">Pix Recebido</p>
                                <p className="text-xs text-gray-500">Maria Oliveira • 09:15</p>
                            </div>
                        </div>
                        <span className="font-semibold text-green-400 text-sm">+ R$ 120,50</span>
                    </div>
                </div>
            </div>
        </div>
    );
};
