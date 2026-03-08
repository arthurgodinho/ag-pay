import React from 'react';
import { ArrowLeft, ArrowDownRight, ArrowUpRight, Search, FileText } from 'lucide-react';
import { Link } from 'react-router-dom';

const mockTransactions = [
    { id: 1, type: 'pix_in', title: 'Pix Recebido', name: 'João da Silva', amount: '85,00', date: 'Hoje, 10:42', status: 'completed' },
    { id: 2, type: 'pix_in', title: 'Pix Recebido', name: 'Maria Oliveira', amount: '120,50', date: 'Hoje, 09:15', status: 'completed' },
    { id: 3, type: 'card_in', title: 'Cartão de Crédito', name: 'Delivery Site', amount: '45,90', date: 'Ontem, 21:30', status: 'completed' },
    { id: 4, type: 'transfer_out', title: 'Transferência Enviada', name: 'Fornecedor Carnes', amount: '450,00', date: 'Ontem, 14:10', status: 'completed' },
    { id: 5, type: 'pix_in', title: 'Pix Recebido', name: 'Carlos Santos', amount: '35,00', date: '15 Mar, 19:40', status: 'completed' },
    { id: 6, type: 'pix_in', title: 'Pix Recebido', name: 'Ana Souza', amount: '90,00', date: '15 Mar, 12:20', status: 'completed' },
];

export const TransactionHistory: React.FC = () => {
    return (
        <div className="flex flex-col min-h-screen bg-black text-white font-sans pb-24">
            <div className="sticky top-0 z-10 bg-black/80 backdrop-blur-md px-6 pt-10 pb-4 border-b border-gray-900">
                <div className="flex items-center justify-between mb-6">
                    <div className="flex items-center">
                        <Link to="/" className="w-10 h-10 flex items-center justify-center rounded-full bg-gray-900 hover:bg-gray-800 transition-colors text-white">
                            <ArrowLeft size={20} />
                        </Link>
                        <h1 className="ml-4 font-semibold tracking-wide text-lg">Extrato</h1>
                    </div>
                    <button className="w-10 h-10 flex items-center justify-center rounded-full bg-gray-900 hover:bg-gray-800 transition-colors text-white">
                        <FileText size={18} />
                    </button>
                </div>

                <div className="relative">
                    <input
                        type="text"
                        placeholder="Buscar transação..."
                        className="w-full bg-gray-900 border border-gray-800 rounded-2xl py-3 pl-12 pr-4 text-sm focus:outline-none focus:border-teal-500 transition-colors text-white placeholder-gray-500"
                    />
                    <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500" size={18} />
                </div>
            </div>

            <div className="px-6 pt-6">
                <div className="flex gap-2 overflow-x-auto hide-scrollbar mb-6 pb-2">
                    <button className="px-4 py-2 rounded-full bg-teal-500 text-black font-medium text-sm whitespace-nowrap">Tudo</button>
                    <button className="px-4 py-2 rounded-full bg-gray-900 text-gray-400 text-sm whitespace-nowrap border border-gray-800">Entradas</button>
                    <button className="px-4 py-2 rounded-full bg-gray-900 text-gray-400 text-sm whitespace-nowrap border border-gray-800">Saídas</button>
                    <button className="px-4 py-2 rounded-full bg-gray-900 text-gray-400 text-sm whitespace-nowrap border border-gray-800">Futuros</button>
                </div>

                <div className="space-y-6">
                    {/* Timestamp Grouping Mock */}
                    <div>
                        <h3 className="text-xs font-semibold text-gray-500 mb-3 tracking-wider uppercase pl-2">Recentes</h3>
                        <div className="space-y-3">
                            {mockTransactions.slice(0, 4).map((tx) => (
                                <div key={tx.id} className="flex items-center justify-between p-4 bg-gray-900/50 rounded-2xl border border-gray-800/50 hover:bg-gray-800/50 transition-colors">
                                    <div className="flex items-center gap-4">
                                        <div className={`w-12 h-12 rounded-full flex items-center justify-center ${tx.type.includes('in') ? 'bg-green-500/10 text-green-500' : 'bg-white/5 text-white'
                                            }`}>
                                            {tx.type.includes('in') ? <ArrowDownRight size={24} /> : <ArrowUpRight size={24} />}
                                        </div>
                                        <div>
                                            <p className="font-medium text-white text-sm">{tx.name}</p>
                                            <p className="text-xs text-gray-500 mt-0.5">{tx.title} • {tx.date}</p>
                                        </div>
                                    </div>
                                    <span className={`font-semibold text-sm tracking-wide ${tx.type.includes('in') ? 'text-green-400' : 'text-white'}`}>
                                        {tx.type.includes('in') ? '+' : '-'} R$ {tx.amount}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </div>

                    <div>
                        <h3 className="text-xs font-semibold text-gray-500 mt-8 mb-3 tracking-wider uppercase pl-2">Anteriores</h3>
                        <div className="space-y-3">
                            {mockTransactions.slice(4).map((tx) => (
                                <div key={tx.id} className="flex items-center justify-between p-4 bg-gray-900/50 rounded-2xl border border-gray-800/50 hover:bg-gray-800/50 transition-colors">
                                    <div className="flex items-center gap-4">
                                        <div className={`w-12 h-12 rounded-full flex items-center justify-center ${tx.type.includes('in') ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500'
                                            }`}>
                                            {tx.type.includes('in') ? <ArrowDownRight size={24} /> : <ArrowUpRight size={24} />}
                                        </div>
                                        <div>
                                            <p className="font-medium text-white text-sm">{tx.name}</p>
                                            <p className="text-xs text-gray-500 mt-0.5">{tx.title} • {tx.date}</p>
                                        </div>
                                    </div>
                                    <span className={`font-semibold text-sm tracking-wide ${tx.type.includes('in') ? 'text-green-400' : 'text-white'}`}>
                                        {tx.type.includes('in') ? '+' : '-'} R$ {tx.amount}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};
