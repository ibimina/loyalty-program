import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { FiCheck, FiLock, FiTarget } from 'react-icons/fi';
import { Achievement } from '../types';

interface AchievementsGridProps {
  allAchievements: Achievement[];
  unlockedAchievements: Achievement[];
  nextAvailable: Achievement[];
}

type FilterType = 'all' | 'unlocked' | 'locked';

export default function AchievementsGrid({
  allAchievements,
  unlockedAchievements,
  nextAvailable,
}: AchievementsGridProps) {
  const [filter, setFilter] = useState<FilterType>('all');
  const unlockedKeys = new Set(unlockedAchievements.map((a) => a.key));
  const nextAvailableKeys = new Set(nextAvailable.map((a) => a.key));

  const filteredAchievements = allAchievements.filter((achievement) => {
    if (filter === 'unlocked') return unlockedKeys.has(achievement.key);
    if (filter === 'locked') return !unlockedKeys.has(achievement.key);
    return true;
  });

  const containerVariants = {
    hidden: { opacity: 0 },
    show: {
      opacity: 1,
      transition: {
        staggerChildren: 0.1,
      },
    },
  };

  const itemVariants = {
    hidden: { opacity: 0, y: 20 },
    show: { opacity: 1, y: 0 },
  };

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ delay: 0.4 }}
      className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6"
    >
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
          Achievements
        </h3>

        {/* Filter tabs */}
        <div className="flex gap-2 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
          {(['all', 'unlocked', 'locked'] as FilterType[]).map((f) => (
            <button
              key={f}
              onClick={() => setFilter(f)}
              className={`px-4 py-2 rounded-md text-sm font-medium transition-colors ${
                filter === f
                  ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow'
                  : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'
              }`}
            >
              {f.charAt(0).toUpperCase() + f.slice(1)}
            </button>
          ))}
        </div>
      </div>

      <motion.div
        variants={containerVariants}
        initial="hidden"
        animate="show"
        className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
      >
        <AnimatePresence mode="popLayout">
          {filteredAchievements.map((achievement) => {
            const isUnlocked = unlockedKeys.has(achievement.key);
            const isNextAvailable = nextAvailableKeys.has(achievement.key);

            return (
              <motion.div
                key={achievement.key}
                variants={itemVariants}
                layout
                exit={{ opacity: 0, scale: 0.8 }}
                whileHover={{ scale: 1.02 }}
                className={`relative p-4 rounded-xl border-2 transition-all ${
                  isUnlocked
                    ? 'bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-green-300 dark:border-green-700'
                    : isNextAvailable
                    ? 'bg-gradient-to-br from-primary-50 to-purple-50 dark:from-primary-900/20 dark:to-purple-900/20 border-primary-300 dark:border-primary-700 border-dashed'
                    : 'bg-gray-50 dark:bg-gray-700/50 border-gray-200 dark:border-gray-600'
                }`}
              >
                {/* Status icon */}
                <div className="absolute top-3 right-3">
                  {isUnlocked ? (
                    <div className="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                      <FiCheck className="w-4 h-4 text-white" />
                    </div>
                  ) : isNextAvailable ? (
                    <div className="w-6 h-6 rounded-full bg-primary-500 flex items-center justify-center animate-pulse">
                      <FiTarget className="w-4 h-4 text-white" />
                    </div>
                  ) : (
                    <div className="w-6 h-6 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                      <FiLock className="w-3 h-3 text-gray-500 dark:text-gray-400" />
                    </div>
                  )}
                </div>

                {/* Achievement icon */}
                <div
                  className={`text-4xl mb-3 ${
                    isUnlocked ? '' : 'grayscale opacity-50'
                  }`}
                >
                  {achievement.icon}
                </div>

                {/* Achievement info */}
                <h4
                  className={`font-semibold mb-1 ${
                    isUnlocked
                      ? 'text-gray-900 dark:text-white'
                      : 'text-gray-500 dark:text-gray-400'
                  }`}
                >
                  {achievement.name}
                </h4>
                <p
                  className={`text-sm ${
                    isUnlocked
                      ? 'text-gray-600 dark:text-gray-300'
                      : 'text-gray-400 dark:text-gray-500'
                  }`}
                >
                  {achievement.description}
                </p>

                {/* Condition */}
                <div className="mt-3 flex items-center gap-2">
                  <span
                    className={`text-xs px-2 py-1 rounded-full ${
                      isUnlocked
                        ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                        : 'bg-gray-100 dark:bg-gray-600 text-gray-600 dark:text-gray-300'
                    }`}
                  >
                    {achievement.condition} {achievement.condition === 1 ? 'purchase' : 'purchases'}
                  </span>
                  {isNextAvailable && !isUnlocked && (
                    <span className="text-xs text-primary-600 dark:text-primary-400 font-medium">
                      Up next!
                    </span>
                  )}
                </div>
              </motion.div>
            );
          })}
        </AnimatePresence>
      </motion.div>

      {filteredAchievements.length === 0 && (
        <div className="text-center py-12 text-gray-500 dark:text-gray-400">
          No achievements found in this category.
        </div>
      )}
    </motion.div>
  );
}
