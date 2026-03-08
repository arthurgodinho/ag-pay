import { useState, useEffect } from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { BottomTabNavigation } from './components/BottomTabNavigation';
import { Dashboard } from './screens/Dashboard';
import { ReceivePix } from './screens/ReceivePix';
import { TransactionHistory } from './screens/TransactionHistory';

export default function App() {
  // Ocultar splash screen ou preloader após montagem
  useEffect(() => {
    // PWA status bar cor em dispositivos que suportam
    document.body.style.backgroundColor = '#000000';
  }, []);

  return (
    <BrowserRouter>
      <div className="min-h-screen bg-black text-white flex flex-col mx-auto max-w-md relative overflow-hidden pb-16 font-sans">

        {/* Renderização Dinâmica de Telas */}
        <div className="flex-1 w-full h-full overflow-y-auto overflow-x-hidden scroll-smooth hide-scrollbar bg-black">
          <Routes>
            <Route path="/" element={<Dashboard />} />
            <Route path="/receive" element={<ReceivePix />} />
            <Route path="/history" element={<TransactionHistory />} />
            <Route path="/profile" element={
              <div className="p-6 text-center text-gray-500 mt-20">
                <span className="material-symbols-outlined text-4xl mb-2">construction</span>
                <p>Perfil em Construção</p>
              </div>
            } />
            <Route path="*" element={<Navigate to="/" replace />} />
          </Routes>
        </div>

        {/* Barra de Navegação Inferior Móvel */}
        <BottomTabNavigation />

      </div>
    </BrowserRouter>
  );
}
