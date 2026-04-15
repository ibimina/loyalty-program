import { motion } from 'framer-motion';
import { FiTrendingUp, FiFlag, FiDollarSign, FiShoppingCart } from 'react-icons/fi';
import { Stats, Badge } from '../types';

interface StatsCardsProps {
  stats: Stats;
  nextBadge: Badge | null;
  remainingToUnlock: number;
  progressPercentage: number;
  purchasesToNextAchievement: number;
}

export default function StatsCards({
  stats,
  nextBadge,
  remainingToUnlock,
  progressPercentage,
  purchasesToNextAchievement,
}: StatsCardsProps) {
  const cards = [
    {
      icon: FiDollarSign,
      label: 'Total Cashback',
      value: `₦${stats.total_cashback_earned.toLocaleString()}`,
      color: 'from-emerald-500 to-green-600',
      bgColor: 'bg-emerald-50 dark:bg-emerald-900/20',
    },
    {
      icon: FiFlag,
      label: 'To Next Badge',
      value: nextBadge ? remainingToUnlock : 0,
      color: 'from-amber-500 to-orange-500',
      bgColor: 'bg-amber-50 dark:bg-amber-900/20',
      helper: nextBadge ? 'achievements left' : 'completed',
    },
    {
      icon: FiTrendingUp,
      label: 'Progress',
      value: `${progressPercentage}%`,
      color: 'from-fuchsia-500 to-pink-500',
      bgColor: 'bg-fuchsia-50 dark:bg-fuchsia-900/20',
      helper: 'towards next badge',
    },
    {
      icon: FiShoppingCart,
      label: 'To Next Achievement',
      value: purchasesToNextAchievement,
      color: 'from-indigo-500 to-violet-600',
      bgColor: 'bg-indigo-50 dark:bg-indigo-900/20',
      helper: purchasesToNextAchievement === 0 ? 'ready to unlock' : 'purchases needed',
    },
  ];

  return (
    <div className="grid grid-cols-2 xl:grid-cols-4 gap-4">
      {cards.map((card, index) => (
        <motion.div
          key={card.label}
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.25 + index * 0.05 }}
          whileHover={{ scale: 1.02 }}
          className={`${card.bgColor} rounded-xl p-4 border border-transparent hover:border-gray-200 dark:hover:border-gray-600 transition-all`}
        >
          <div className={`w-10 h-10 rounded-lg bg-gradient-to-br ${card.color} flex items-center justify-center mb-3`}>
            <card.icon className="w-5 h-5 text-white" />
          </div>
          <p className="text-2xl font-bold text-gray-900 dark:text-white">
            {card.value}
          </p>
          <p className="text-sm text-gray-500 dark:text-gray-400">
            {card.label}
          </p>
          {'helper' in card && card.helper && (
            <p className="text-xs text-gray-400 dark:text-gray-500 mt-1">{card.helper}</p>
          )}
        </motion.div>
      ))}
    </div>
  );
}
