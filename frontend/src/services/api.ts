import axios from 'axios';
import { AchievementsResponse, HistoryResponse } from '../types';

// Create axios instance with base configuration
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

/**
 * Fetch user achievements and badge information
 */
export const fetchUserAchievements = async (userId: number): Promise<AchievementsResponse> => {
  const response = await api.get<AchievementsResponse>(`/users/${userId}/achievements`);
  return response.data;
};

/**
 * Fetch user achievement history timeline
 */
export const fetchAchievementHistory = async (userId: number): Promise<HistoryResponse> => {
  const response = await api.get<HistoryResponse>(`/users/${userId}/achievements/history`);
  return response.data;
};

/**
 * Simulate a purchase (for demo purposes)
 */
export const simulatePurchase = async (userId: number, amount: number, productName?: string) => {
  const response = await api.post(`/users/${userId}/purchases`, {
    amount,
    product_name: productName || 'Demo Product',
  });
  return response.data;
};

/**
 * Reset demo progress for a user.
 */
export const resetUserProgress = async (userId: number) => {
  const response = await api.post(`/users/${userId}/reset-progress`);
  return response.data;
};

export default api;
