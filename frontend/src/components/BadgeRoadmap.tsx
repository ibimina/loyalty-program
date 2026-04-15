import { motion } from 'framer-motion';
import { Badge } from '../types';

interface BadgeRoadmapProps {
  badges: Badge[];
  currentBadgeKey: string;
}

export default function BadgeRoadmap({ badges, currentBadgeKey }: BadgeRoadmapProps) {
  const currentIndex = badges.findIndex((b) => b.key === currentBadgeKey);
  const safeCurrentIndex = currentIndex < 0 ? 0 : currentIndex;
  const progressPercent = badges.length > 1
    ? (safeCurrentIndex / (badges.length - 1)) * 100
    : 100;
  const nextBadge = badges[safeCurrentIndex + 1] ?? null;

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ delay: 0.35 }}
      className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6"
    >
      <div className="mb-5 flex flex-wrap items-center justify-between gap-3">
        <h3 className="text-xl font-semibold text-gray-900 dark:text-white">
          Badge Journey
        </h3>
        <span className="inline-flex items-center rounded-full bg-primary-100 px-3 py-1 text-xs font-semibold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">
          Current: {badges[safeCurrentIndex]?.name}
        </span>
      </div>

      <div className="mb-6 rounded-xl border border-gray-200 bg-gray-50/70 p-4 dark:border-gray-700 dark:bg-gray-900/30">
        <div className="mb-2 flex items-center justify-between text-xs font-medium text-gray-600 dark:text-gray-300">
          <span>Tier Progress</span>
          <span>{safeCurrentIndex + 1}/{badges.length} unlocked</span>
        </div>
        <div className="h-2 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
          <motion.div
            initial={{ width: 0 }}
            animate={{ width: `${progressPercent}%` }}
            transition={{ duration: 0.8, ease: 'easeOut' }}
            className="h-full rounded-full bg-gradient-to-r from-primary-500 via-sky-400 to-teal-400"
          />
        </div>
        <p className="mt-2 text-xs text-gray-500 dark:text-gray-400">
          {nextBadge
            ? `Next target: ${nextBadge.name} (${nextBadge.achievements_required} achievements)`
            : 'You have reached the highest tier.'}
        </p>
      </div>

      <div className="pb-2">
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
          {badges.map((badge, index) => {
            const isUnlocked = index <= safeCurrentIndex;
            const isCurrent = badge.key === currentBadgeKey;
            const isNext = index === safeCurrentIndex + 1;

            return (
              <motion.div
                key={badge.key}
                initial={{ opacity: 0, y: 14 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: 0.4 + index * 0.08 }}
                className={`relative flex min-h-[184px] w-full flex-col items-center justify-center rounded-2xl border p-4 text-center transition-all duration-300 ${isCurrent
                    ? 'border-primary-400 bg-primary-50/70 shadow-md dark:border-primary-600 dark:bg-primary-900/20'
                    : isUnlocked
                      ? 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800'
                      : 'border-gray-200 bg-gray-50/70 dark:border-gray-700 dark:bg-gray-800/60'
                  }`}
              >
                {isCurrent && (
                  <span className="absolute right-2 top-2 inline-flex h-5 w-5 items-center justify-center rounded-full bg-primary-500 text-[11px] font-bold text-white">
                    ✓
                  </span>
                )}

                {isNext && !isCurrent && (
                  <span className="absolute left-2 top-2 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                    NEXT
                  </span>
                )}

                <div
                  className="mb-3 flex h-14 w-14 items-center justify-center rounded-full shadow-sm"
                  style={{
                    background: isUnlocked
                      ? `linear-gradient(135deg, ${badge.color}33, ${badge.color}88)`
                      : 'rgba(156, 163, 175, 0.2)',
                  }}
                >
                  <span className={`text-2xl ${isUnlocked ? '' : 'grayscale opacity-45'}`}>
                    {badge.icon}
                  </span>
                </div>

                <p
                  className={`text-sm font-semibold ${isUnlocked ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'
                    }`}
                >
                  {badge.name}
                </p>

                <p className="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  {badge.achievements_required}{' '}
                  {badge.achievements_required === 1 ? 'achievement' : 'achievements'}
                </p>

                {badge.cashback_amount > 0 && (
                  <p
                    className={`mt-1 text-xs font-medium ${isUnlocked ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500'
                      }`}
                  >
                    +₦{badge.cashback_amount}
                  </p>
                )}
              </motion.div>
            );
          })}
        </div>
      </div>
    </motion.div>
  );
}
