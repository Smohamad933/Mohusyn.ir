import React from 'react';

/** Full-page soft gradient backdrop (inspired by apple-notch.vercel.app):
 *  deep dark base with slow-drifting violet / blue / pink glows. */
const GradientBackground: React.FC = () => (
  <div className="gradient-bg" aria-hidden="true">
    <div className="gradient-blob b1" />
    <div className="gradient-blob b2" />
    <div className="gradient-blob b3" />
    <div className="gradient-blob b4" />
    <div className="gradient-noise" />
  </div>
);

export default GradientBackground;
