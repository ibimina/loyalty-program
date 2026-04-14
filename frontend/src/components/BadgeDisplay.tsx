import { motion } from 'framer-motion';
import { Badge } from '../types';

interface BadgeDisplayProps {
  currentBadge: Badge;
  nextBadge: Badge | null;
  progressPercentage: number;
  remainingToUnlock: number;
}

const getBadgeGradient = (key: string): string => {
  const gradients: Record<string, string> = {
    beginner: 'from-amber-700 to-amber-900',
    bronze: 'from-amber-500 to-orange-600',
    silver: 'from-gray-300 to-gray-500',
    gold: 'from-yellow-400 to-amber-500',
    platinum: 'from-slate-300 to-blue-400',
  };
  return gradients[key] || gradients.beginner;
};

export default function BadgeDisplay({
  currentBadge,
  nextBadge,
  progressPercentage,
  remainingToUnlock,
}: BadgeDisplayProps) {
  return (
    <motion.div
      initial={{ opacity: 0, x: 20 }}
      animate={{ opacity: 1, x: 0 }}
      transition={{ delay: 0.2 }}
      className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 card-hover relative overflow-hidden"
    >
      {/* Background decoration */}
      <div className="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-primary-500/10 to-purple-500/10 rounded-full -translate-y-20 translate-x-20" />
      
      <div className="relative">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          Current Badge
        </h3>

        <div className="flex items-center gap-6">
          {/* Current Badge */}
          <motion.div
            initial={{ scale: 0 }}
            animate={{ scale: 1 }}
            transition={{ type: 'spring', stiffness: 200, damping: 15, delay: 0.3 }}
            className="relative"
          >
            <div
              className={`w-28 h-28 rounded-full bg-gradient-to-br ${getBadgeGradient(
                currentBadge.key
              )} flex items-center justify-center shadow-xl animate-glow`}
            >
              <span className="text-5xl">{currentBadge.icon}</span>
            </div>
            {/* Shine effect */}
            <div className="absolute inset-0 rounded-full badge-shimmer" />
          </motion.div>

          {/* Badge Info */}
          <div className="flex-1">
            <motion.h4
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.4 }}
              className="text-2xl font-bold text-gray-900 dark:text-white"
            >
              {currentBadge.name}
            </motion.h4>
            <p className="text-gray-500 dark:text-gray-400 mt-1">
              {currentBadge.description}
            </p>

            {nextBadge && (
              <div className="mt-4">
                <p className="text-sm text-gray-600 dark:text-gray-300">
                  <span className="font-medium">{remainingToUnlock}</span> more{' '}
                  {remainingToUnlock === 1 ? 'achievement' : 'achievements'} to unlock{' '}
                  <span className="font-semibold text-primary-500">
                    {nextBadge.icon} {nextBadge.name}
                  </span>
                </p>

                {/* Mini progress bar */}
                <div className="mt-2 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                  <motion.div
                    initial={{ width: 0 }}
                    animate={{ width: `${progressPercentage}%` }}
                    transition={{ duration: 1, delay: 0.5 }}
                    className="h-full bg-gradient-to-r from-primary-500 to-purple-500 rounded-full"
                  />
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </motion.div>
  );
}
