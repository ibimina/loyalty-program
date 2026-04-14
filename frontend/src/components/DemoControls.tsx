import { useState } from 'react';
import { motion } from 'framer-motion';
import { FiPlay, FiLoader } from 'react-icons/fi';

interface DemoControlsProps {
  onSimulatePurchase: () => Promise<void>;
}

export default function DemoControls({ onSimulatePurchase }: DemoControlsProps) {
  const [busy, setBusy] = useState(false);

  const handleClick = async () => {
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

        <motion.button
          whileHover={{ scale: 1.02 }}
          whileTap={{ scale: 0.98 }}
          disabled={busy}
          onClick={handleClick}
          className="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-primary-600 to-primary-500 text-white font-medium shadow-md disabled:opacity-70"
        >
          {busy ? <FiLoader className="w-4 h-4 animate-spin" /> : <FiPlay className="w-4 h-4" />}
          {busy ? 'Processing...' : 'Simulate Purchase'}
        </motion.button>
      </div>
    </motion.section>
  );
}
