import { Router } from 'express';
import { login, getMe } from '../controllers/AuthController';
import { getSettings, updateSettings } from '../controllers/SettingsController';
import { authMiddleware, adminMiddleware } from '../middlewares/auth.validation';

const routes = Router();

// =======================
// Autenticação
// =======================
routes.post('/auth/login', login);
routes.get('/auth/me', authMiddleware, getMe);

// =======================
// Configurações do Banco
// =======================
routes.get('/settings', authMiddleware, getSettings);
routes.put('/admin/settings', authMiddleware, adminMiddleware, updateSettings);

export default routes;
