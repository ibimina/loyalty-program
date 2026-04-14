import { motion } from 'framer-motion';
import { Badge } from '../types';

interface ProgressSectionProps {
  currentBadge: Badge;
  nextBadge: Badge | null;
  progressPercentage: number;
  remainingToUnlock: number;
  achievementsUnlocked: number;
}

export default function ProgressSection({
  currentBadge,
  nextBadge,
  progressPercentage,
  remainingToUnlock,
  achievementsUnlocked,
}: ProgressSectionProps) {
  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ delay: 0.3 }}
      className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6"
    >
      <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
        Badge Progress
      </h3>

      {nextBadge ? (
        <>
          {/* Progress visualization */}
          <div className="flex items-center gap-4 mb-4">
            {/* Current badge mini */}
            <div className="flex flex-col items-center">
              <div className="w-14 h-14 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center shadow-md">
                <span className="text-2xl">{currentBadge.icon}</span>
              </div>
              <span className="text-xs mt-1 text-gray-500 dark:text-gray-400">
                {currentBadge.name}
              </span>
            </div>

            {/* Progress bar */}
            <div className="flex-1">
              <div className="h-4 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden relative">
                <motion.div
                  initial={{ width: 0 }}
                  animate={{ width: `${progressPercentage}%` }}
                  transition={{ duration: 1.5, ease: 'easeOut' }}
                  className="h-full bg-gradient-to-r from-primary-500 via-purple-500 to-pink-500 rounded-full relative"
                >
                  {/* Animated shine */}
                  <div className="absolute inset-0 badge-shimmer" />
                </motion.div>
                
                {/* Progress percentage */}
                <div className="absolute inset-0 flex items-center justify-center">
                  <motion.span
                    initial={{ opacity: 0 }}
                    animate={{ opacity: 1 }}
                    transition={{ delay: 1 }}
                    className="text-xs font-bold text-gray-700 dark:text-gray-200"
                  >
                    {progressPercentage}%
                  </motion.span>
                </div>
              </div>

              {/* Milestones */}
              <div className="flex justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
                <span>{currentBadge.achievements_required} achievements</span>
                <span className="font-medium text-primary-500">
                  {nextBadge.achievements_required} achievements
                </span>
              </div>
            </div>

            {/* Next badge mini */}
            <div className="flex flex-col items-center">
              <div className="w-14 h-14 rounded-full bg-gradient-to-br from-primary-100 to-purple-100 dark:from-primary-900/30 dark:to-purple-900/30 flex items-center justify-center shadow-md border-2 border-dashed border-primary-300 dark:border-primary-700">
                <span className="text-2xl grayscale opacity-50">{nextBadge.icon}</span>
              </div>
              <span className="text-xs mt-1 text-gray-500 dark:text-gray-400">
                {nextBadge.name}
              </span>
            </div>
          </div>

          {/* Encouragement message */}
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ delay: 0.5 }}
            className="text-center py-4 bg-gradient-to-r from-primary-50 to-purple-50 dark:from-primary-900/20 dark:to-purple-900/20 rounded-xl"
          >
            <p className="text-gray-700 dark:text-gray-300">
              🎯 You need{' '}
              <span className="font-bold text-primary-600 dark:text-primary-400">
                {remainingToUnlock}
              </span>{' '}
              more {remainingToUnlock === 1 ? 'achievement' : 'achievements'} to reach{' '}
              <span className="font-bold">{nextBadge.name}</span>!
            </p>
            {nextBadge.cashback_amount > 0 && (
              <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Unlock it and earn <span className="font-semibold text-green-600">₦{nextBadge.cashback_amount}</span> cashback! 💰
              </p>
            )}
          </motion.div>
        </>
      ) : (
        <div className="text-center py-8">
          <div className="text-5xl mb-4">🏆</div>
          <h4 className="text-xl font-bold text-gray-900 dark:text-white">
            Congratulations!
          </h4>
          <p className="text-gray-600 dark:text-gray-300 mt-2">
            You've reached the highest badge tier: <strong>{currentBadge.name}</strong>!
          </p>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
            You've unlocked {achievementsUnlocked} achievements. Keep shopping!
          </p>
        </div>
      )}
    </motion.div>
  );
}
