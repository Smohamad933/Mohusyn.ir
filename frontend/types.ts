import React from 'react';

export interface NavLink { label: string; href: string; }

export interface Project {
  id: string; title: string; meta: string; action: string; image: string; url: string;
  featured?: boolean; description?: string; videoUrl?: string; code?: string; codeFilename?: string; actionType?: 'link' | 'video' | 'code'; order?: number; published?: boolean;
}
export interface Resource { id: string; title: string; size: string; date: string; url: string; published?: boolean; }
export interface FeedPost { id: string; image: string; date: string; text: string; likes: number; comments: number; url: string; published?: boolean; }
export interface TimelineItem { id: string; year: string; title: string; tag: string; text: string; published?: boolean; }
export interface Testimonial { id: string; name: string; role: string; text: string; avatar: string; rating: number; verified: boolean; createdAt: string; approved: boolean; }
export interface GalleryPhoto { id: string; src: string; title: string; caption: string; category: string; published?: boolean; }
export interface Track { id: string; title: string; artist: string; url: string; cover: string; category: string; }
export interface MusicFolder { key: string; label: string; emoji: string; spooky?: boolean; }
export interface VaultItem { id: string; secretCode: string; type: 'image' | 'video' | 'audio' | 'pdf' | 'link'; src: string; title: string; thumbnail: string; folderName: string; published?: boolean; }

export interface SocialLink { platform: string; url: string; icon: React.ComponentType; }
export interface Achievement { id: number; icon: string; text: string; }

export interface Settings {
  siteName: string; firstName: string; fullName: string; tagline: string; subtitle: string; description: string;
  heroPrefix: string; roles: string[]; heroImage: string; aboutImage: string; ogImage: string; bannerImage: string; showBanner: boolean;
  ctaPrimary: { label: string; href: string }; ctaSecondary: { label: string; href: string; show: boolean };
  badges: { emoji: string; label: string; value: string }[];
  socials: { github: string; facebook: string; linkedin: string; instagram: string; youtube: string; email: string; phone: string; whatsapp: string; location: string; githubRepo: string };
  stack: { name: string; icon: string }[];
  about: { kicker: string; roleLine: string; p1: string; p2: string; stats: { value: string; label: string }[]; tech: string[]; cards: { title: string; text: string }[]; topSkill: string; passion: string; country: string; openToWork: boolean };
  sectionTitles: Record<string, { title: string; subtitle: string }>;
  sections: Record<string, boolean>;
  feedbackStats: { satisfaction: string; reviews: string; rating: string; verified: string; footer: string };
  contact: { availability: string; projectsDone: string; };
  feed: { network: string; profileUrl: string; profilePic: string };
  footer: { text: string; quote: string };
  music: { autoplay: boolean; folders: MusicFolder[]; tracks: Track[] };
  seo: { title: string; description: string; keywords: string; url: string };
  welcome: { enabled: boolean; text: string };
  vaultPassword: string;
}

export interface StaticPageItem { id: string; slug: string; title: string; body: string; updatedAt?: string; published?: boolean; }

export interface Content {
  settings: Settings;
  pages: StaticPageItem[];
  projects: Project[];
  resources: Resource[];
  feed: FeedPost[];
  timeline: TimelineItem[];
  testimonials: Testimonial[];
  gallery: GalleryPhoto[];
  vault: VaultItem[];
}
