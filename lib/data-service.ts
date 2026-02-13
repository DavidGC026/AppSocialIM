import { Activity, MOCK_ACTIVITIES } from '@/lib/social-data';

export class DataService {
  static async getMixedFeed(token: string): Promise<Activity[]> {
    // In standalone mode, just return the mock activities
    // Simulate network delay
    await new Promise(resolve => setTimeout(resolve, 800));
    return MOCK_ACTIVITIES;
  }
}
