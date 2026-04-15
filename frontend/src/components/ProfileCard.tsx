import { motion } from 'framer-motion';
import { FiMail, FiUser } from 'react-icons/fi';
import { UserData } from '../types';

interface ProfileCardProps {
  user: UserData;
}

export default function ProfileCard({ user }: ProfileCardProps) {
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
          <p className="text-gray-500 dark:text-gray-400 text-sm flex items-center gap-1 mt-1">
            <FiUser className="w-4 h-4" />
            Loyalty Member
          </p>
          <p className="text-gray-500 dark:text-gray-400 text-sm flex items-center gap-1">
            <FiMail className="w-4 h-4" />
            {user.email}
          </p>
        </div>
      </div>

      <div className="rounded-xl bg-gray-50 dark:bg-gray-700/40 p-4 border border-gray-100 dark:border-gray-700">
        <p className="text-sm text-gray-600 dark:text-gray-300">
          Track your rewards journey, unlock achievements, and earn cashback as you move through badge tiers.
        </p>
      </div>
    </motion.div>
  );
}
