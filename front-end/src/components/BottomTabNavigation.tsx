import React from 'react';
import { NavLink } from 'react-router-dom';
import { Home, QrCode, ReceiptText, User } from 'lucide-react';

export const BottomTabNavigation: React.FC = () => {
    return (
        <div className="fixed bottom-0 left-0 right-0 max-w-md mx-auto bg-gray-900 border-t border-gray-800 pb-safe z-50">
            <div className="flex justify-around items-center h-16 px-2">
                <NavLink
                    to="/"
                    end
                    className={({ isActive }) =>
                        `flex flex-col items-center justify-center w-16 h-full gap-1 transition-colors ${isActive ? 'text-teal-400' : 'text-gray-500 hover:text-gray-400'}`
                    }
                >
                    <Home size={24} />
                    <span className="text-[10px] font-medium tracking-wide">Início</span>
                </NavLink>

                <NavLink
                    to="/receive"
                    className={({ isActive }) =>
                        `flex flex-col items-center justify-center w-16 h-full gap-1 transition-colors ${isActive ? 'text-teal-400' : 'text-gray-500 hover:text-gray-400'}`
                    }
                >
                    <div className="bg-teal-500 p-2 rounded-full text-black shadow-lg shadow-teal-500/20 mb-3 -mt-4 border-4 border-black">
                        <QrCode size={26} />
                    </div>
                </NavLink>

                <NavLink
                    to="/history"
                    className={({ isActive }) =>
                        `flex flex-col items-center justify-center w-16 h-full gap-1 transition-colors ${isActive ? 'text-teal-400' : 'text-gray-500 hover:text-gray-400'}`
                    }
                >
                    <ReceiptText size={24} />
                    <span className="text-[10px] font-medium tracking-wide">Extrato</span>
                </NavLink>

                <NavLink
                    to="/profile"
                    className={({ isActive }) =>
                        `flex flex-col items-center justify-center w-16 h-full gap-1 transition-colors ${isActive ? 'text-teal-400' : 'text-gray-500 hover:text-gray-400'}`
                    }
                >
                    <User size={24} />
                    <span className="text-[10px] font-medium tracking-wide">Perfil</span>
                </NavLink>
            </div>
        </div>
    );
};
