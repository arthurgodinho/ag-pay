import React, { useState } from 'react';
import { LayoutDashboard, ArrowRightLeft, Users, BarChart2, Settings, User, Bell, CheckCircle, Clock, Search, Filter, ChevronRight } from 'lucide-react';
import { LineChart, Line, ResponsiveContainer, PieChart, Pie, Cell } from 'recharts';

const lineData1 = [
  { value: 400 }, { value: 300 }, { value: 550 }, { value: 450 }, { value: 700 }
];
const lineData2 = [
  { value: 200 }, { value: 400 }, { value: 300 }, { value: 600 }, { value: 500 }
];
const lineData3 = [
  { value: 800 }, { value: 700 }, { value: 600 }, { value: 400 }, { value: 300 }
];

const pieData = [
  { name: 'Marketplace', value: 60 },
  { name: 'Sellers', value: 40 },
];
const COLORS = ['#2dd4bf', '#86efac'];

export default function AdminApp() {
  const [activeTab, setActiveTab] = useState('dashboard');

  return (
    <div className="flex flex-col h-full bg-[#0f172a] font-sans relative overflow-hidden">
      {/* Background Pattern Simulation */}
      <div className="absolute inset-0 opacity-[0.03] pointer-events-none" style={{ backgroundImage: 'radial-gradient(#2dd4bf 1px, transparent 1px)', backgroundSize: '24px 24px' }}></div>
      <div className="absolute inset-0 opacity-10 pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/circuit-board.png')]"></div>

      {/* Header */}
      <header className="bg-[#2e1065] flex justify-between items-center p-5 pt-8 z-10 shadow-md">
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-full bg-black/40 border border-teal-500 flex items-center justify-center text-xs text-teal-400 font-bold shadow-[0_0_10px_rgba(45,212,191,0.3)]">
            AG
          </div>
          <h1 className="text-lg font-medium text-white tracking-wide">Painel Administrativo</h1>
        </div>
        <div className="flex items-center gap-4 text-white">
          <button className="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors">
            <User size={18} />
          </button>
          <button className="relative hover:text-gray-300 transition-colors">
            <Bell size={20} />
            <span className="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 border-2 border-[#2e1065] rounded-full"></span>
          </button>
        </div>
      </header>

      {/* Main Content */}
      <main className="flex-1 overflow-y-auto px-4 py-6 pb-28 z-10 hide-scrollbar">
        
        {activeTab === 'dashboard' && (
          <div className="space-y-5 animate-in">
            {/* TPV Card */}
            <AdminCard title="Volume Total Transacionado (TPV)" value="R$ 2.540.000,00" data={lineData1} color="#2dd4bf" />

            {/* Lucro Card */}
            <AdminCard title="Lucro Líquido (Taxas)" value="R$ 127.000,00" data={lineData2} color="#2dd4bf" />

            {/* Saques Card */}
            <AdminCard title="Saques Pendentes" value="R$ 450.000,00" data={lineData3} color="#fb923c" borderColor="border-orange-400/50" shadowColor="shadow-[0_0_15px_rgba(251,146,60,0.1)]" />

            {/* Split Card */}
            <div className="bg-[#0f172a]/90 backdrop-blur-md border border-teal-500/30 rounded-2xl p-5 shadow-[0_0_15px_rgba(45,212,191,0.05)] relative overflow-hidden">
              <div className="absolute -right-10 -bottom-10 w-40 h-40 bg-teal-500/5 rounded-full blur-2xl"></div>
              <h2 className="text-gray-300 text-sm mb-4 font-medium">Monitoramento de Split</h2>
              <div className="h-44 flex items-center justify-center relative">
                <ResponsiveContainer width="100%" height="100%">
                  <PieChart>
                    <Pie
                      data={pieData}
                      cx="50%"
                      cy="50%"
                      innerRadius={0}
                      outerRadius={70}
                      dataKey="value"
                      stroke="none"
                    >
                      {pieData.map((entry, index) => (
                        <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                      ))}
                    </Pie>
                  </PieChart>
                </ResponsiveContainer>
              </div>
              <div className="flex justify-center gap-6 mt-4 text-xs font-medium">
                <div className="flex items-center gap-2">
                  <div className="w-3 h-3 bg-[#2dd4bf] rounded-sm"></div>
                  <span className="text-gray-300">Marketplace (60%)</span>
                </div>
                <div className="flex items-center gap-2">
                  <div className="w-3 h-3 bg-[#86efac] rounded-sm"></div>
                  <span className="text-gray-300">Sellers (40%)</span>
                </div>
              </div>
            </div>

            {/* Clientes Card */}
            <div className="bg-[#0f172a]/90 backdrop-blur-md border border-teal-500/30 rounded-2xl p-5 shadow-[0_0_15px_rgba(45,212,191,0.05)] relative overflow-hidden">
              {/* Globe decoration */}
              <div className="absolute -right-12 -bottom-12 opacity-10 pointer-events-none">
                <svg width="150" height="150" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="50" cy="50" r="48" stroke="#2dd4bf" strokeWidth="1"/>
                  <ellipse cx="50" cy="50" rx="20" ry="48" stroke="#2dd4bf" strokeWidth="1"/>
                  <ellipse cx="50" cy="50" rx="48" ry="20" stroke="#2dd4bf" strokeWidth="1"/>
                  <path d="M2 50H98" stroke="#2dd4bf" strokeWidth="1"/>
                  <path d="M50 2V98" stroke="#2dd4bf" strokeWidth="1"/>
                </svg>
              </div>

              <h2 className="text-gray-300 text-sm mb-5 font-medium">Gestão de Clientes</h2>
              <div className="space-y-4">
                <ClientRow name="Merchant A" status="Aprovado" icon={<CheckCircle size={18} className="text-teal-400" />} />
                <ClientRow name="Merchant B" status="Pendente" icon={<Clock size={18} className="text-orange-400" />} statusColor="text-orange-400" />
                <ClientRow name="Merchant C" status="Aprovado" icon={<CheckCircle size={18} className="text-teal-400" />} />
              </div>
            </div>
          </div>
        )}

        {activeTab === 'transacoes' && (
          <div className="animate-in">
            <div className="flex justify-between items-center mb-6">
              <h2 className="text-2xl font-bold text-white">Transações</h2>
              <div className="flex gap-3">
                <button className="w-10 h-10 rounded-full bg-[#1e293b] flex items-center justify-center text-teal-400 hover:bg-white/5 transition-colors border border-teal-500/20">
                  <Search size={20} />
                </button>
                <button className="w-10 h-10 rounded-full bg-[#1e293b] flex items-center justify-center text-teal-400 hover:bg-white/5 transition-colors border border-teal-500/20">
                  <Filter size={20} />
                </button>
              </div>
            </div>
            
            <div className="space-y-4">
              <TransactionRow id="#TRX-8921" merchant="Merchant A" amount="R$ 1.250,00" status="Aprovada" date="14:30" />
              <TransactionRow id="#TRX-8920" merchant="Merchant C" amount="R$ 450,00" status="Aprovada" date="14:15" />
              <TransactionRow id="#TRX-8919" merchant="Merchant B" amount="R$ 8.900,00" status="Em Análise" date="13:45" isPending />
              <TransactionRow id="#TRX-8918" merchant="Merchant A" amount="R$ 120,00" status="Aprovada" date="12:20" />
              <TransactionRow id="#TRX-8917" merchant="Merchant C" amount="R$ 3.400,00" status="Recusada" date="11:10" isFailed />
            </div>
          </div>
        )}

        {activeTab === 'clientes' && (
          <div className="animate-in">
            <div className="flex justify-between items-center mb-6">
              <h2 className="text-2xl font-bold text-white">Clientes</h2>
              <button className="px-4 py-2 bg-teal-500 text-black font-medium rounded-lg hover:bg-teal-400 transition-colors text-sm">
                + Novo Cliente
              </button>
            </div>
            
            <div className="space-y-4">
              <MerchantCard name="Merchant A" tpv="R$ 1.2M" status="Ativo" plan="Pro" />
              <MerchantCard name="Merchant B" tpv="R$ 450K" status="Em Análise" plan="Basic" isPending />
              <MerchantCard name="Merchant C" tpv="R$ 890K" status="Ativo" plan="Enterprise" />
            </div>
          </div>
        )}

        {activeTab === 'configuracoes' && (
          <div className="animate-in">
            <h2 className="text-2xl font-bold text-white mb-6">Configurações</h2>
            
            <div className="space-y-3">
              <SettingsOption title="Taxas e Tarifas" desc="Configure as taxas padrão e personalizadas" />
              <SettingsOption title="Integrações" desc="Adquirentes, Antifraude e APIs" />
              <SettingsOption title="Regras de Split" desc="Gerencie o split de pagamentos" />
              <SettingsOption title="Usuários e Permissões" desc="Controle de acesso ao painel" />
              <SettingsOption title="Segurança" desc="2FA, Logs de auditoria e IP Whitelist" />
            </div>
          </div>
        )}

        {activeTab === 'relatorios' && (
          <div className="animate-in flex flex-col items-center justify-center h-64 text-center">
            <BarChart2 size={48} className="text-teal-500/50 mb-4" />
            <h3 className="text-xl font-bold text-white mb-2">Relatórios Avançados</h3>
            <p className="text-gray-400 text-sm max-w-[250px]">Gere relatórios detalhados de vendas, recebíveis e conciliação.</p>
            <button className="mt-6 px-6 py-2 bg-[#1e293b] border border-teal-500/30 text-teal-400 rounded-lg font-medium hover:bg-[#1e293b]/80 transition-colors">
              Gerar Novo Relatório
            </button>
          </div>
        )}

      </main>

      {/* Bottom Nav */}
      <nav className="absolute bottom-0 w-full bg-[#0f172a]/95 backdrop-blur-md border-t border-teal-500/20 flex justify-around py-4 pb-8 z-20">
        <NavItem icon={<LayoutDashboard size={22} />} label="Dashboard" active={activeTab === 'dashboard'} onClick={() => setActiveTab('dashboard')} />
        <NavItem icon={<ArrowRightLeft size={22} />} label="Transações" active={activeTab === 'transacoes'} onClick={() => setActiveTab('transacoes')} />
        <NavItem icon={<Users size={22} />} label="Clientes" active={activeTab === 'clientes'} onClick={() => setActiveTab('clientes')} />
        <NavItem icon={<BarChart2 size={22} />} label="Relatórios" active={activeTab === 'relatorios'} onClick={() => setActiveTab('relatorios')} />
        <NavItem icon={<Settings size={22} />} label="Configurações" active={activeTab === 'configuracoes'} onClick={() => setActiveTab('configuracoes')} />
      </nav>
    </div>
  );
}

function AdminCard({ title, value, data, color, borderColor = "border-teal-500/30", shadowColor = "shadow-[0_0_15px_rgba(45,212,191,0.05)]" }: any) {
  return (
    <div className={`bg-[#0f172a]/90 backdrop-blur-md border ${borderColor} rounded-2xl p-5 ${shadowColor} flex justify-between items-center relative overflow-hidden group hover:border-opacity-60 transition-all cursor-pointer`}>
      <div className="absolute -left-10 -top-10 w-32 h-32 bg-teal-500/5 rounded-full blur-2xl group-hover:bg-teal-500/10 transition-colors"></div>
      <div className="z-10">
        <h2 className="text-gray-300 text-sm mb-1.5 font-medium">{title}</h2>
        <div className={`text-2xl font-bold tracking-tight ${color === '#fb923c' ? 'text-orange-400' : 'text-white'}`}>{value}</div>
      </div>
      <div className="w-28 h-14 z-10">
        <ResponsiveContainer width="100%" height="100%">
          <LineChart data={data}>
            <Line type="monotone" dataKey="value" stroke={color} strokeWidth={2.5} dot={false} isAnimationActive={true} />
          </LineChart>
        </ResponsiveContainer>
      </div>
    </div>
  );
}

function ClientRow({ name, status, icon, statusColor = "text-teal-400" }: any) {
  return (
    <div className="flex items-center justify-between text-sm p-2 hover:bg-white/5 rounded-lg transition-colors cursor-pointer">
      <div className="flex items-center gap-3">
        {icon}
        <span className="text-gray-200 font-medium">{name}</span>
      </div>
      <div className="flex items-center gap-2">
        <span className="text-gray-600">-</span>
        <span className={`${statusColor} font-medium`}>{status}</span>
      </div>
    </div>
  );
}

function TransactionRow({ id, merchant, amount, status, date, isPending, isFailed }: any) {
  return (
    <div className="bg-[#1e293b]/50 border border-teal-500/10 rounded-xl p-4 flex justify-between items-center hover:bg-[#1e293b] transition-colors cursor-pointer">
      <div>
        <div className="flex items-center gap-2 mb-1">
          <span className="text-white font-bold">{amount}</span>
          <span className={`text-[10px] px-2 py-0.5 rounded-full font-medium ${isFailed ? 'bg-red-500/20 text-red-400' : isPending ? 'bg-orange-500/20 text-orange-400' : 'bg-teal-500/20 text-teal-400'}`}>
            {status}
          </span>
        </div>
        <div className="text-xs text-gray-400 flex items-center gap-2">
          <span>{id}</span>
          <span>•</span>
          <span>{merchant}</span>
        </div>
      </div>
      <div className="text-xs text-gray-500 font-medium">{date}</div>
    </div>
  );
}

function MerchantCard({ name, tpv, status, plan, isPending }: any) {
  return (
    <div className="bg-[#1e293b]/50 border border-teal-500/20 rounded-xl p-5 relative overflow-hidden group hover:border-teal-500/50 transition-colors cursor-pointer">
      <div className="flex justify-between items-start mb-4">
        <div>
          <h3 className="text-white font-bold text-lg">{name}</h3>
          <p className="text-sm text-gray-400">Plano {plan}</p>
        </div>
        <span className={`text-xs px-2.5 py-1 rounded-full font-medium ${isPending ? 'bg-orange-500/20 text-orange-400' : 'bg-teal-500/20 text-teal-400'}`}>
          {status}
        </span>
      </div>
      <div className="flex justify-between items-end">
        <div>
          <p className="text-xs text-gray-500 mb-1">TPV Mensal</p>
          <p className="text-teal-400 font-bold">{tpv}</p>
        </div>
        <button className="text-gray-400 hover:text-white transition-colors">
          <ChevronRight size={20} />
        </button>
      </div>
    </div>
  );
}

function SettingsOption({ title, desc }: any) {
  return (
    <div className="bg-[#1e293b]/50 border border-teal-500/10 rounded-xl p-4 flex justify-between items-center hover:bg-[#1e293b] transition-colors cursor-pointer">
      <div>
        <h4 className="text-white font-medium">{title}</h4>
        <p className="text-xs text-gray-400 mt-0.5">{desc}</p>
      </div>
      <ChevronRight size={20} className="text-gray-500" />
    </div>
  );
}

function NavItem({ icon, label, active, onClick }: { icon: React.ReactNode, label: string, active: boolean, onClick: () => void }) {
  return (
    <button onClick={onClick} className={`flex flex-col items-center gap-1.5 transition-colors ${active ? 'text-teal-400' : 'text-gray-500 hover:text-gray-400'}`}>
      {icon}
      <span className="text-[10px] font-medium tracking-wide">{label}</span>
    </button>
  );
}
