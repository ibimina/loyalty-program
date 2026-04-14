// API Response Types
export interface UserData {
  id: number;
  name: string;
  email: string;
}

export interface Achievement {
  key: string;
  name: string;
  description: string;
  icon: string;
  condition: number;
  unlocked?: boolean;
  unlocked_at?: string;
}

export interface Badge {
  key: string;
  name: string;
  description: string;
  icon: string;
  color: string;
  achievements_required: number;
  cashback_amount: number;
  is_current?: boolean;
  is_unlocked?: boolean;
}

export interface Stats {
  total_purchases: number;
  total_achievements_unlocked: number;
  total_achievements_available: number;
  total_badges: number;
  total_cashback_earned: number;
}

export interface AchievementsResponse {
  success: boolean;
  data: {
    user: UserData;
    unlocked_achievements: string[];
    next_available_achievements: string[];
    current_badge: string;
    next_badge: string | null;
    remaining_to_unlock_next_badge: number;
    progress_percentage: number;
    achievements: {
      unlocked: Achievement[];
      next_available: Achievement[];
      all: Achievement[];
    };
    badges: {
      current: Badge;
      next: Badge | null;
      all: Badge[];
    };
    stats: Stats;
  };
}

export interface HistoryItem {
  achievement: {
    key: string;
    name: string;
    icon: string;
  };
  unlocked_at: string;
  unlocked_at_human: string;
}

export interface HistoryResponse {
  success: boolean;
  data: {
    history: HistoryItem[];
    count: number;
  };
}
