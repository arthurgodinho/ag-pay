import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import { PrismaClient } from '@prisma/client';

dotenv.config();

const app = express();
const prisma = new PrismaClient();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(express.json());

import apiRoutes from './routes';
app.use('/api', apiRoutes);

// Rota de Teste de Saúde
app.get('/health', (req, res) => {
    res.json({ status: 'ok', message: 'AG PAY Backend is running!' });
});

// Iniciando o Servidor
app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});
