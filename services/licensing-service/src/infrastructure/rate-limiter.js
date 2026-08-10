export class RateLimiter {
  constructor({ limit = 60, windowMs = 60000, clock = () => Date.now() } = {}) {
    this.limit = Math.max(1, limit);
    this.windowMs = Math.max(1000, windowMs);
    this.clock = clock;
    this.buckets = new Map();
  }

  consume(key) {
    const now = this.clock();
    const bucket = this.buckets.get(key);
    if (!bucket || bucket.resetAt <= now) {
      this.buckets.set(key, { count: 1, resetAt: now + this.windowMs });
      return { remaining: this.limit - 1, resetAt: now + this.windowMs };
    }
    bucket.count += 1;
    if (bucket.count > this.limit) throw new Error('rate_limited');
    if (this.buckets.size > 10000) {
      for (const [candidate, value] of this.buckets) if (value.resetAt <= now) this.buckets.delete(candidate);
    }
    return { remaining: this.limit - bucket.count, resetAt: bucket.resetAt };
  }
}
