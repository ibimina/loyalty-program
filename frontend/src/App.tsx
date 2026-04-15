import { useState, useEffect, useCallback } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import Confetti from 'react-confetti';
import toast from 'react-hot-toast';

import { AchievementsResponse } from './types';
import { fetchUserAchievements, simulatePurchase } from './services/api';

import Header from './components/Header';
import ProfileCard from './components/ProfileCard';
import BadgeDisplay from './components/BadgeDisplay';
import ProgressSection from './components/ProgressSection';
import AchievementsGrid from './components/AchievementsGrid';
import BadgeRoadmap from './components/BadgeRoadmap';
import StatsCards from './components/StatsCards';
import Skeleton from './components/Skeleton';
import DemoControls from './components/DemoControls';

// Default demo user ID (in real app, this would come from auth)
const DEMO_USER_ID = 1;

function App() {
  const [data, setData] = useState<AchievementsResponse['data'] | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [showConfetti, setShowConfetti] = useState(false);
  const [darkMode, setDarkMode] = useState(() => {
    return localStorage.getItem('darkMode') === 'true' ||
      window.matchMedia('(prefers-color-scheme: dark)').matches;
  });

  // Apply dark mode class to document
  useEffect(() => {
    document.documentElement.classList.toggle('dark', darkMode);
    localStorage.setItem('darkMode', String(darkMode));
  }, [darkMode]);

  // Fetch data
  const loadData = useCallback(async () => {
    try {
      setLoading(true);
      const response = await fetchUserAchievements(DEMO_USER_ID);
      setData(response.data);
      setError(null);
      return response.data;
    } catch (err) {
      console.error('Failed to fetch achievements:', err);
      setError('Failed to load achievements. Please try again.');
      return null;
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadData();
  }, [loadData]);

  // Handle simulated purchase
  const handleSimulatePurchase = async () => {
    try {
      const previousBadge = data?.badges.current.key;
      const previousAchievements = data?.achievements.unlocked.length ?? 0;

      await simulatePurchase(DEMO_USER_ID, Math.floor(Math.random() * 5000) + 1000);
      const refreshed = await loadData();

      if (!refreshed) {
        toast.error('Purchase recorded, but refresh failed.');
        return;
      }

      // Check for new achievements/badges
      const newAchievements = refreshed.achievements.unlocked.length - previousAchievements;
      const newBadge = refreshed.badges.current.key !== previousBadge;

      if (newBadge) {
        setShowConfetti(true);
        toast.success(`🏆 New Badge Unlocked: ${refreshed.badges.current.name}! +₦300 Cashback!`, {
          duration: 5000,
        });
        setTimeout(() => setShowConfetti(false), 5000);
      } else if (newAchievements > 0) {
        toast.success(`🎉 Achievement Unlocked!`, {
          duration: 3000,
        });
      } else {
        toast.success('Purchase recorded!');
      }
    } catch (err) {
      toast.error('Failed to simulate purchase');
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 transition-colors duration-300">
      {/* Confetti animation for badge unlock */}
      <AnimatePresence>
        {showConfetti && (
          <Confetti
            width={window.innerWidth}
            height={window.innerHeight}
            recycle={false}
            numberOfPieces={500}
            gravity={0.3}
          />
        )}
      </AnimatePresence>

      <Header darkMode={darkMode} onToggleDarkMode={() => setDarkMode(!darkMode)} />

      <main className="container mx-auto px-4 py-8 max-w-7xl">
        {error && (
          <motion.div
            initial={{ opacity: 0, y: -20 }}
            animate={{ opacity: 1, y: 0 }}
            className="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-6"
          >
            {error}
            <button onClick={loadData} className="ml-4 underline">
              Retry
            </button>
          </motion.div>
        )}

        {loading ? (
          <Skeleton />
        ) : data ? (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 0.5 }}
            className="space-y-8"
          >
            {(() => {
              const nextLockedAchievement = data.achievements.all
                .filter((a) => !a.unlocked)
                .sort((a, b) => a.condition - b.condition)[0];

              const purchasesToNextAchievement = nextLockedAchievement
                ? Math.max(0, nextLockedAchievement.condition - data.stats.total_purchases)
                : 0;

              return (
                <>
                  {/* Top Section: Profile & Current Badge */}
                  <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <ProfileCard user={data.user} />
                    <div className="lg:col-span-2">
                      <BadgeDisplay
                        currentBadge={data.badges.current}
                        nextBadge={data.badges.next}
                        progressPercentage={data.progress_percentage}
                        remainingToUnlock={data.remaining_to_unlock_next_badge}
                      />
                    </div>
                  </div>

                  {/* Stats Section */}
                  <StatsCards
                    stats={data.stats}
                    nextBadge={data.badges.next}
                    remainingToUnlock={data.remaining_to_unlock_next_badge}
                    progressPercentage={data.progress_percentage}
                    purchasesToNextAchievement={purchasesToNextAchievement}
                  />

                  {/* Progress Section */}
                  <ProgressSection
                    currentBadge={data.badges.current}
                    nextBadge={data.badges.next}
                    progressPercentage={data.progress_percentage}
                    remainingToUnlock={data.remaining_to_unlock_next_badge}
                    achievementsUnlocked={data.achievements.unlocked.length}
                  />

                  {/* Demo Controls */}
                  <DemoControls onSimulatePurchase={handleSimulatePurchase} />

                  {/* Badge Roadmap */}
                  <BadgeRoadmap badges={data.badges.all} currentBadgeKey={data.badges.current.key} />

                  {/* Achievements Grid */}
                  <AchievementsGrid
                    allAchievements={data.achievements.all}
                    unlockedAchievements={data.achievements.unlocked}
                    nextAvailable={data.achievements.next_available}
                  />
                </>
              );
            })()}
          </motion.div>
        ) : null}
      </main>

      {/* Footer */}
      <footer className="py-6 text-center text-gray-500 dark:text-gray-400 text-sm">
        <p>Loyalty Program Dashboard • Built with ❤️</p>
      </footer>
    </div>
  );
}

export default App;
