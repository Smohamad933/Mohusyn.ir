import React from 'react';
import { Github, Facebook, Linkedin, Mail, Instagram, Youtube, Phone, MessageCircle, Send } from 'lucide-react';
import { useSettings } from '../../context/ContentContext';

/** Social icon row driven by settings.socials — hides anything that's empty. */
const SocialIcons: React.FC<{ size?: number; className?: string; itemClass?: string }> = ({ size = 22, className = '', itemClass = '' }) => {
  const s = useSettings().socials;
  const items = [
    { key: 'email', href: s.email ? `mailto:${s.email}` : '', Icon: Mail, hover: 'hover:text-red-500' },
    { key: 'linkedin', href: s.linkedin, Icon: Linkedin, hover: 'hover:text-blue-500' },
    { key: 'telegram', href: s.telegram ? (s.telegram.startsWith('http') ? s.telegram : `https://t.me/${s.telegram.replace('@', '')}`) : '', Icon: Send, hover: 'hover:text-sky-400' },
    { key: 'instagram', href: s.instagram, Icon: Instagram, hover: 'hover:text-pink-500' },
    { key: 'github', href: s.github, Icon: Github, hover: 'hover:text-slate-900 dark:hover:text-white' },
    { key: 'facebook', href: s.facebook, Icon: Facebook, hover: 'hover:text-blue-600' },
    { key: 'youtube', href: s.youtube, Icon: Youtube, hover: 'hover:text-red-500' },
    { key: 'whatsapp', href: s.whatsapp ? `https://wa.me/${s.whatsapp.replace(/[^0-9]/g, '')}` : '', Icon: MessageCircle, hover: 'hover:text-green-500' },
    { key: 'phone', href: s.phone ? `tel:${s.phone}` : '', Icon: Phone, hover: 'hover:text-emerald-500' },
  ].filter(i => i.href);
  return (
    <div className={`flex items-center justify-center gap-6 pt-6 lg:justify-start text-slate-400 ${className}`}>
      {items.map(({ key, href, Icon, hover }) => (
        <a key={key} href={href} target={href.startsWith('http') ? '_blank' : undefined} rel="noopener noreferrer" aria-label={key}
           data-track={`social:${key}`} className={`transition-all hover:scale-110 ${hover} ${itemClass}`}>
          <Icon size={size} />
        </a>
      ))}
    </div>
  );
};
export default SocialIcons;
