import { Request, Response } from 'express';
import bcrypt from 'bcryptjs';
import jwt from 'jsonwebtoken';
import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

export const login = async (req: Request, res: Response) => {
    try {
        const { email, password } = req.body;

        if (!email || !password) {
            return res.status(400).json({ error: 'Email e senha são obrigatórios' });
        }

        const user = await prisma.user.findUnique({ where: { email } });

        if (!user || !(await bcrypt.compare(password, user.password))) {
            return res.status(401).json({ error: 'Credenciais inválidas' });
        }

        if (!user.status) {
            return res.status(403).json({ error: 'Conta de usuário desativada' });
        }

        const token = jwt.sign(
            { userId: user.id, role: user.role },
            process.env.JWT_SECRET as string,
            { expiresIn: '12h' }
        );

        res.json({
            token,
            user: {
                id: user.id,
                name: user.name,
                email: user.email,
                role: user.role
            }
        });
    } catch (error) {
        console.error('Erro no Login:', error);
        res.status(500).json({ error: 'Erro interno no servidor' });
    }
};

export const getMe = async (req: Request, res: Response) => {
    try {
        const reqUser = (req as any).user;
        const user = await prisma.user.findUnique({
            where: { id: reqUser.userId },
            select: { id: true, name: true, email: true, role: true, status: true, createdAt: true }
        });

        if (!user) {
            return res.status(404).json({ error: 'Usuário não encontrado' });
        }

        res.json(user);
    } catch (error) {
        console.error('Erro em getMe:', error);
        res.status(500).json({ error: 'Erro interno no servidor' });
    }
};
