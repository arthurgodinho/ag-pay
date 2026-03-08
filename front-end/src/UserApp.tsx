import React, { useState } from 'react';
import { Home, List, DollarSign, User, Eye, HelpCircle, Bell, ArrowRightLeft, Download, BarChart2, QrCode, EyeOff, ChevronRight, Search, Filter } from 'lucide-react';

export default function UserApp() {
  const [activeTab, setActiveTab] = useState('inicio');
  const [showBalance, setShowBalance] = useState(true);

  return (
    <div className="flex flex-col h-full bg-[#0b1426] font-sans relative overflow-hidden">
      {/* Header */}
      <header className="flex justify-between items-center p-6 pt-8 z-10">
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-full bg-teal-500/20 flex items-center justify-center text-teal-400">
            <User size={24} />
          </div>
          <h1 className="text-xl font-medium text-white">Olá, <span className="font-bold">Gabriela</span></h1>
        </div>
        <div className="flex items-center gap-4 text-teal-400">
          <button onClick={() => setShowBalance(!showBalance)} className="hover:text-teal-300 transition-colors">
            {showBalance ? <Eye size={24} /> : <EyeOff size={24} />}
          </button>
          <button className="hover:text-teal-300 transition-colors">
            <HelpCircle size={24} />
          </button>
          <button className="relative hover:text-teal-300 transition-colors">
            <Bell size={24} />
            <span className="absolute top-0 right-0 w-2.5 h-2.5 bg-red-500 border-2 border-[#0b1426] rounded-full"></span>
          </button>
        </div>
      </header>

      {/* Main Content */}
      <main className="flex-1 overflow-y-auto px-6 pb-24 z-10 hide-scrollbar">
        {activeTab === 'inicio' && (
          <>
            {/* Balance Card */}
            <div className="bg-[#162032] rounded-2xl p-6 mb-8 shadow-lg border border-white/5">
              <h2 className="text-gray-300 text-lg mb-2 font-medium">Conta</h2>
              <div className="text-4xl font-bold text-teal-400 tracking-tight">
                {showBalance ? 'R$ 1.356,98' : 'R$ •••••••'}
              </div>
            </div>

            {/* Quick Actions */}
            <div className="flex justify-between mb-8 px-2">
              <ActionBtn icon={<QrCode size={28} />} label="Pix" />
              <ActionBtn icon={<ArrowRightLeft size={28} />} label="Transferir" />
              <ActionBtn icon={<Download size={28} />} label="Sacar" />
              <ActionBtn icon={<BarChart2 size={28} />} label="Relatórios" />
            </div>

            {/* Rates Card */}
            <div className="bg-[#162032] rounded-2xl p-6 shadow-lg border border-white/5">
              <h2 className="text-xl font-bold text-white mb-5">Minhas Taxas</h2>
              <div className="space-y-4 text-gray-300">
                <div className="flex items-center justify-between">
                  <span className="text-base">Crédito à vista:</span>
                  <span className="text-teal-400 font-bold text-lg">3,5%</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-base">Débito:</span>
                  <span className="text-teal-400 font-bold text-lg">1,2%</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-base">Crédito parcelado:</span>
                  <span className="text-teal-400 font-bold text-lg">4,5% + 1,5%/mês</span>
                </div>
              </div>
            </div>
          </>
        )}

        {activeTab === 'extrato' && (
          <div className="animate-in fade-in slide-in-from-bottom-4 duration-300">
            <div className="flex justify-between items-center mb-6">
              <h2 className="text-2xl font-bold text-white">Extrato</h2>
              <div className="flex gap-3">
                <button className="w-10 h-10 rounded-full bg-[#162032] flex items-center justify-center text-teal-400 hover:bg-white/5 transition-colors">
                  <Search size={20} />
                </button>
                <button className="w-10 h-10 rounded-full bg-[#162032] flex items-center justify-center text-teal-400 hover:bg-white/5 transition-colors">
                  <Filter size={20} />
                </button>
              </div>
            </div>
            
            <div className="space-y-4">
              <TransactionItem title="Transferência Recebida" desc="João Silva" amount="+ R$ 450,00" date="Hoje, 14:30" type="in" />
              <TransactionItem title="Pagamento Pix" desc="Mercado Livre" amount="- R$ 120,50" date="Ontem, 09:15" type="out" />
              <TransactionItem title="Saque" desc="Conta Corrente" amount="- R$ 200,00" date="15 Mar, 18:45" type="out" />
              <TransactionItem title="Venda Maquininha" desc="Crédito à vista" amount="+ R$ 89,90" date="14 Mar, 12:20" type="in" />
              <TransactionItem title="Venda Link de Pagamento" desc="Crédito parcelado" amount="+ R$ 350,00" date="12 Mar, 16:10" type="in" />
            </div>
          </div>
        )}

        {activeTab === 'cobrar' && (
          <div className="animate-in fade-in slide-in-from-bottom-4 duration-300">
            <h2 className="text-2xl font-bold text-white mb-6">Cobrar</h2>
            <div className="grid grid-cols-2 gap-4">
              <ChargeOption icon={<QrCode size={32} />} title="Cobrar com Pix" desc="Gere um QR Code" />
              <ChargeOption icon={<ArrowRightLeft size={32} />} title="Link de Pagamento" desc="Envie por WhatsApp" />
              <ChargeOption icon={<BarChart2 size={32} />} title="Assinatura" desc="Cobrança recorrente" />
              <ChargeOption icon={<Download size={32} />} title="Boleto" desc="Emita um boleto" />
            </div>
          </div>
        )}

        {activeTab === 'perfil' && (
          <div className="animate-in fade-in slide-in-from-bottom-4 duration-300">
            <h2 className="text-2xl font-bold text-white mb-6">Meu Perfil</h2>
            <div className="bg-[#162032] rounded-2xl p-6 shadow-lg border border-white/5 mb-6 flex items-center gap-4">
              <div className="w-16 h-16 rounded-full bg-teal-500/20 flex items-center justify-center text-teal-400">
                <User size={32} />
              </div>
              <div>
                <h3 className="text-lg font-bold text-white">Gabriela Santos</h3>
                <p className="text-sm text-gray-400">gabriela@exemplo.com</p>
                <p className="text-sm text-teal-400 mt-1">Conta Verificada</p>
              </div>
            </div>
            
            <div className="space-y-2">
              <ProfileOption title="Dados Pessoais" />
              <ProfileOption title="Contas Bancárias" />
              <ProfileOption title="Configurações de Segurança" />
              <ProfileOption title="Ajuda e Suporte" />
              <ProfileOption title="Sair do Aplicativo" isDestructive />
            </div>
          </div>
        )}
      </main>

      {/* Bottom Nav */}
      <nav className="absolute bottom-0 w-full bg-[#0b1426] border-t border-gray-800 flex justify-around py-4 pb-8 z-20">
        <NavItem icon={<Home size={24} />} label="Início" active={activeTab === 'inicio'} onClick={() => setActiveTab('inicio')} />
        <NavItem icon={<List size={24} />} label="Extrato" active={activeTab === 'extrato'} onClick={() => setActiveTab('extrato')} />
        <NavItem icon={<DollarSign size={24} />} label="Cobrar" active={activeTab === 'cobrar'} onClick={() => setActiveTab('cobrar')} />
        <NavItem icon={<User size={24} />} label="Perfil" active={activeTab === 'perfil'} onClick={() => setActiveTab('perfil')} />
      </nav>
    </div>
  );
}

function ActionBtn({ icon, label }: { icon: React.ReactNode, label: string }) {
  return (
    <div className="flex flex-col items-center gap-3 cursor-pointer group">
      <button className="w-16 h-16 rounded-full bg-teal-500/10 flex items-center justify-center text-teal-400 group-hover:bg-teal-500/20 group-active:scale-95 transition-all">
        {icon}
      </button>
      <span className="text-sm text-gray-300 font-medium">{label}</span>
    </div>
  );
}

function NavItem({ icon, label, active, onClick }: { icon: React.ReactNode, label: string, active: boolean, onClick: () => void }) {
  return (
    <button onClick={onClick} className={`flex flex-col items-center gap-1.5 transition-colors ${active ? 'text-teal-400' : 'text-gray-500 hover:text-gray-400'}`}>
      {icon}
      <span className="text-xs font-medium">{label}</span>
    </button>
  );
}

function TransactionItem({ title, desc, amount, date, type }: any) {
  return (
    <div className="bg-[#162032] rounded-xl p-4 flex justify-between items-center border border-white/5">
      <div className="flex items-center gap-4">
        <div className={`w-10 h-10 rounded-full flex items-center justify-center ${type === 'in' ? 'bg-teal-500/10 text-teal-400' : 'bg-red-500/10 text-red-400'}`}>
          {type === 'in' ? <ArrowRightLeft size={20} /> : <ArrowRightLeft size={20} className="rotate-180" />}
        </div>
        <div>
          <h4 className="text-white font-medium">{title}</h4>
          <p className="text-xs text-gray-400">{desc}</p>
        </div>
      </div>
      <div className="text-right">
        <p className={`font-bold ${type === 'in' ? 'text-teal-400' : 'text-white'}`}>{amount}</p>
        <p className="text-xs text-gray-500">{date}</p>
      </div>
    </div>
  );
}

function ChargeOption({ icon, title, desc }: any) {
  return (
    <div className="bg-[#162032] rounded-2xl p-5 flex flex-col items-center text-center gap-3 border border-white/5 hover:border-teal-500/30 transition-colors cursor-pointer group">
      <div className="text-teal-400 group-hover:scale-110 transition-transform">
        {icon}
      </div>
      <div>
        <h3 className="text-white font-bold text-sm">{title}</h3>
        <p className="text-xs text-gray-400 mt-1">{desc}</p>
      </div>
    </div>
  );
}

function ProfileOption({ title, isDestructive = false }: any) {
  return (
    <button className="w-full bg-[#162032] rounded-xl p-4 flex justify-between items-center border border-white/5 hover:bg-white/5 transition-colors">
      <span className={`font-medium ${isDestructive ? 'text-red-400' : 'text-white'}`}>{title}</span>
      <ChevronRight size={20} className={isDestructive ? 'text-red-400' : 'text-gray-500'} />
    </button>
  );
}
