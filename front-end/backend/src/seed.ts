import { PrismaClient } from '@prisma/client';
import bcrypt from 'bcryptjs';

const prisma = new PrismaClient();

async function main() {
    console.log('Seeding Database...');

    // Criar Configurações Default Adquirente
    await prisma.setting.create({
        data: {
            active_acquirer: 'pagarme',
            pix_fee_percent: 1.00,
            pix_fee_fixed: 0.00,
            cc_fee_percent: 3.99,
            cc_installments_fee: 1.50,
            receipt_deadline_days: 1
        }
    });

    // Criar Usuário Lojista (Comum)
    await prisma.user.create({
        data: {
            name: 'Demo Lojista',
            email: 'lojista@agpay.com',
            password: await bcrypt.hash('123456', 10),
            role: 'USER',
            status: true
        }
    });

    // Criar Usuário Admin (AG Soluções)
    await prisma.user.create({
        data: {
            name: 'Equipe AG Soluções',
            email: 'admin@agsolucoes.com.br',
            password: await bcrypt.hash('123456', 10),
            role: 'SUPER_ADMIN',
            status: true
        }
    });

    console.log('Database Seeded Successfully!');
}

main()
    .catch((e) => {
        console.error(e);
        process.exit(1);
    })
    .finally(async () => {
        await prisma.$disconnect();
    });
