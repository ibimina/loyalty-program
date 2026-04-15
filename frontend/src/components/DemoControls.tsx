import { useState } from 'react';
import { motion } from 'framer-motion';
import { FiPlay, FiLoader, FiRotateCcw } from 'react-icons/fi';

interface DemoControlsProps {
  onSimulatePurchase: () => Promise<void>;
  onResetProgress: () => Promise<void>;
}

export default function DemoControls({ onSimulatePurchase, onResetProgress }: DemoControlsProps) {
  const [busy, setBusy] = useState(false);

  const handleSimulate = async () => {
    if (busy) {
      return;
    }

    try {
      setBusy(true);
      await onSimulatePurchase();
    } finally {
      setBusy(false);
    }
  };

  const handleReset = async () => {
    if (busy) {
      return;
    }

    try {
      setBusy(true);
      await onResetProgress();
    } finally {
      setBusy(false);
    }
  };

  return (
    <motion.section
      initial={{ opacity: 0, y: 16 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ delay: 0.45 }}
      className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6"
    >
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white">Demo Controls</h3>
          <p className="text-sm text-gray-600 dark:text-gray-300 mt-1">
            Simulate a purchase to test achievement unlocks, badge progression, and cashback events.
          </p>
        </div>

        <div className="flex items-center gap-3">
          <motion.button
            whileHover={{ scale: 1.02 }}
            whileTap={{ scale: 0.98 }}
            disabled={busy}
            onClick={handleReset}
            className="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100 font-medium border border-gray-200 dark:border-gray-600 disabled:opacity-70"
          >
            {busy ? <FiLoader className="w-4 h-4 animate-spin" /> : <FiRotateCcw className="w-4 h-4" />}
            Reset Progress
          </motion.button>

          <motion.button
            whileHover={{ scale: 1.02 }}
            whileTap={{ scale: 0.98 }}
            disabled={busy}
            onClick={handleSimulate}
            className="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-primary-600 to-primary-500 text-white font-medium shadow-md disabled:opacity-70"
          >
            {busy ? <FiLoader className="w-4 h-4 animate-spin" /> : <FiPlay className="w-4 h-4" />}
            {busy ? 'Processing...' : 'Simulate Purchase'}
          </motion.button>
        </div>
      </div>
    </motion.section>
  );
}
