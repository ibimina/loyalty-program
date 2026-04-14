import { motion } from 'framer-motion';
import { FiUser, FiMail, FiShoppingBag, FiAward } from 'react-icons/fi';
import { UserData, Stats } from '../types';

interface ProfileCardProps {
  user: UserData;
  stats: Stats;
}

export default function ProfileCard({ user, stats }: ProfileCardProps) {
  return (
    <motion.div
      initial={{ opacity: 0, x: -20 }}
      animate={{ opacity: 1, x: 0 }}
      transition={{ delay: 0.1 }}
      className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 card-hover"
    >
      <div className="flex items-center gap-4 mb-6">
        {/* Avatar */}
        <div className="w-16 h-16 rounded-full bg-gradient-to-br from-primary-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold">
          {user.name.charAt(0).toUpperCase()}
        </div>
        <div>
          <h2 className="text-xl font-bold text-gray-900 dark:text-white">
            {user.name}
          </h2>
          <p className="text-gray-500 dark:text-gray-400 text-sm flex items-center gap-1">
            <FiMail className="w-4 h-4" />
            {user.email}
          </p>
        </div>
      </div>

      {/* Quick Stats */}
      <div className="space-y-3">
        <div className="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
          <span className="flex items-center gap-2 text-gray-600 dark:text-gray-300">
            <FiShoppingBag className="w-4 h-4 text-primary-500" />
            Total Purchases
          </span>
          <span className="font-semibold text-gray-900 dark:text-white">
            {stats.total_purchases}
          </span>
        </div>
        <div className="flex items-center justify-between py-2">
          <span className="flex items-center gap-2 text-gray-600 dark:text-gray-300">
            <FiAward className="w-4 h-4 text-yellow-500" />
            Achievements
          </span>
          <span className="font-semibold text-gray-900 dark:text-white">
            {stats.total_achievements_unlocked}/{stats.total_achievements_available}
          </span>
        </div>
      </div>
    </motion.div>
  );
}
