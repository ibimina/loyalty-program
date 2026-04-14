import { motion } from 'framer-motion';
import { FiShoppingBag, FiAward, FiTarget, FiStar, FiDollarSign } from 'react-icons/fi';
import { Stats } from '../types';

interface StatsCardsProps {
  stats: Stats;
}

export default function StatsCards({ stats }: StatsCardsProps) {
  const cards = [
    {
      icon: FiShoppingBag,
      label: 'Total Purchases',
      value: stats.total_purchases,
      color: 'from-blue-500 to-cyan-500',
      bgColor: 'bg-blue-50 dark:bg-blue-900/20',
    },
    {
      icon: FiAward,
      label: 'Achievements Unlocked',
      value: stats.total_achievements_unlocked,
      color: 'from-green-500 to-emerald-500',
      bgColor: 'bg-green-50 dark:bg-green-900/20',
    },
    {
      icon: FiTarget,
      label: 'Achievements Available',
      value: stats.total_achievements_available,
      color: 'from-purple-500 to-pink-500',
      bgColor: 'bg-purple-50 dark:bg-purple-900/20',
    },
    {
      icon: FiStar,
      label: 'Badge Tiers',
      value: stats.total_badges,
      color: 'from-yellow-500 to-orange-500',
      bgColor: 'bg-yellow-50 dark:bg-yellow-900/20',
    },
    {
      icon: FiDollarSign,
      label: 'Total Cashback',
      value: `₦${stats.total_cashback_earned.toLocaleString()}`,
      color: 'from-emerald-500 to-green-600',
      bgColor: 'bg-emerald-50 dark:bg-emerald-900/20',
    },
  ];

  return (
    <div className="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
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
        </motion.div>
      ))}
    </div>
  );
}
