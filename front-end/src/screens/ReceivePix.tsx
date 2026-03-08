import React, { useState } from 'react';
import { ArrowLeft, Delete, QrCode } from 'lucide-react';
import { Link } from 'react-router-dom';

export const ReceivePix: React.FC = () => {
    const [amount, setAmount] = useState('0');
    const [isGenerated, setIsGenerated] = useState(false);

    const handleNumberClick = (num: string) => {
        if (amount === '0') {
            setAmount(num);
        } else if (amount.length < 8) { // Limite simples max 99.999,99
            setAmount(amount + num);
        }
    };

    const handleDeleteClick = () => {
        if (amount.length > 1) {
            setAmount(amount.slice(0, -1));
        } else {
            setAmount('0');
        }
    };

    const formattedAmount = (parseInt(amount) / 100).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const handleGenerate = () => {
        if (parseInt(amount) > 0) {
            setIsGenerated(true);
        }
    };

    if (isGenerated) {
        return (
            <div className="flex flex-col min-h-screen bg-black text-white font-sans">
                <div className="p-6 flex items-center">
                    <button onClick={() => setIsGenerated(false)} className="w-10 h-10 flex items-center justify-center rounded-full bg-gray-900 border border-gray-800 text-white">
                        <ArrowLeft size={20} />
                    </button>
                    <h1 className="ml-4 font-semibold tracking-wide">Cobrança Gerada</h1>
                </div>

                <div className="flex-1 flex flex-col items-center pt-8 px-6">
                    <p className="text-gray-400 mb-2 font-medium tracking-wide">VALOR DA COBRANÇA</p>
                    <h2 className="text-5xl font-bold text-teal-400 mb-10 tracking-tighter">R$ {formattedAmount}</h2>

                    <div className="bg-white p-4 rounded-3xl shadow-2xl shadow-teal-500/20 mb-8 border-4 border-gray-800 relative">
                        {/* Placeholder QR CODE Mockado */}
                        <div className="absolute -top-4 -right-4 bg-teal-500 text-black text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest shadow-lg">Mock</div>
                        <img src={`https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=MockAGPAY_${amount}`} alt="QR Code Pix" className="w-64 h-64 rounded-xl" />
                    </div>

                    <p className="text-center text-sm text-gray-500 px-4 mb-4 leading-relaxed">Mostre este código para o cliente escanear com o aplicativo do banco dele.</p>
                </div>
            </div>
        );
    }

    return (
        <div className="flex flex-col min-h-screen bg-black text-white font-sans pb-24">
            <div className="p-6 flex items-center border-b border-gray-900">
                <Link to="/" className="w-10 h-10 flex items-center justify-center rounded-full bg-gray-900 border border-gray-800 hover:bg-gray-800 transition-colors text-white">
                    <ArrowLeft size={20} />
                </Link>
                <h1 className="ml-4 font-semibold tracking-wide">Receber Caixa</h1>
            </div>

            <div className="flex-1 flex flex-col items-center justify-center p-6">
                <p className="text-gray-400 mb-4 font-medium tracking-widest text-xs uppercase">Digite o Valor</p>
                <div className="text-5xl font-bold tracking-tighter text-white mb-2 py-4 border-b border-gray-800 w-full text-center">
                    R$ <span className={amount === '0' ? 'text-gray-600' : 'text-teal-400'}>{formattedAmount}</span>
                </div>
            </div>

            <div className="px-6 pb-6">
                <div className="grid grid-cols-3 gap-4 mb-8">
                    {['1', '2', '3', '4', '5', '6', '7', '8', '9'].map(num => (
                        <button key={num} onClick={() => handleNumberClick(num)} className="h-16 rounded-2xl bg-gray-900 border border-gray-800 text-2xl font-medium active:bg-gray-800 active:scale-95 transition-all outline-none">
                            {num}
                        </button>
                    ))}
                    <button className="h-16 rounded-2xl text-2xl font-medium"></button>
                    <button onClick={() => handleNumberClick('0')} className="h-16 rounded-2xl bg-gray-900 border border-gray-800 text-2xl font-medium active:bg-gray-800 active:scale-95 transition-all outline-none">
                        0
                    </button>
                    <button onClick={handleDeleteClick} className="h-16 rounded-2xl bg-gray-900 border border-gray-800 text-xl font-medium flex items-center justify-center text-gray-400 active:bg-gray-800 active:scale-95 transition-all outline-none">
                        <Delete size={28} />
                    </button>
                </div>

                <button
                    onClick={handleGenerate}
                    disabled={amount === '0'}
                    className={`w-full h-16 rounded-2xl font-semibold text-lg flex items-center justify-center gap-2 transition-all ${amount === '0' ? 'bg-gray-800 text-gray-600 cursor-not-allowed' : 'bg-teal-500 text-black shadow-lg shadow-teal-500/25 active:scale-95'}`}
                >
                    <QrCode size={20} />
                    Gerar Pix Dinâmico
                </button>
            </div>
        </div>
    );
};
