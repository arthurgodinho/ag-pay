import { Request, Response } from 'express';
import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

export const getSettings = async (req: Request, res: Response) => {
    try {
        const settings = await prisma.setting.findFirst();
        if (!settings) {
            // Retorna defaults transparentes se não existir linha no BD
            return res.json({
                active_acquirer: 'pagarme',
                pix_fee_percent: 1.00,
                pix_fee_fixed: 0.00,
                cc_fee_percent: 3.99,
                cc_installments_fee: 1.50,
                receipt_deadline_days: 1
            });
        }

        // Escondendo credenciais puras pra frontend por segurança
        const { acquirer_credentials, ...safeSettings } = settings;
        res.json(safeSettings);
    } catch (error) {
        console.error('Erro ao buscar Settings:', error);
        res.status(500).json({ error: 'Erro interno no servidor' });
    }
};

export const updateSettings = async (req: Request, res: Response) => {
    try {
        const data = req.body;

        const currentSettings = await prisma.setting.findFirst();
        let updated;

        if (currentSettings) {
            updated = await prisma.setting.update({
                where: { id: currentSettings.id },
                data: {
                    active_acquirer: data.active_acquirer ?? currentSettings.active_acquirer,
                    pix_fee_percent: data.pix_fee_percent ?? currentSettings.pix_fee_percent,
                    pix_fee_fixed: data.pix_fee_fixed ?? currentSettings.pix_fee_fixed,
                    cc_fee_percent: data.cc_fee_percent ?? currentSettings.cc_fee_percent,
                    cc_installments_fee: data.cc_installments_fee ?? currentSettings.cc_installments_fee,
                    receipt_deadline_days: data.receipt_deadline_days ?? currentSettings.receipt_deadline_days,
                }
            });
        } else {
            updated = await prisma.setting.create({
                data: {
                    active_acquirer: data.active_acquirer ?? 'pagarme',
                    pix_fee_percent: data.pix_fee_percent ?? 1.00,
                    pix_fee_fixed: data.pix_fee_fixed ?? 0.00,
                    cc_fee_percent: data.cc_fee_percent ?? 3.99,
                    cc_installments_fee: data.cc_installments_fee ?? 1.50,
                    receipt_deadline_days: data.receipt_deadline_days ?? 1,
                }
            });
        }

        const { acquirer_credentials, ...safeSettings } = updated;
        res.json(safeSettings);
    } catch (error) {
        console.error('Erro ao atualizar Settings:', error);
        res.status(500).json({ error: 'Erro interno no servidor' });
    }
};
