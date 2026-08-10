import { createHash } from 'node:crypto';
import { createReadStream } from 'node:fs';
import { realpath, stat } from 'node:fs/promises';
import path from 'node:path';
import { verifyDownloadGrant } from '../domain/signing.js';

async function sha256(filename) {
  const hash = createHash('sha256');
  for await (const chunk of createReadStream(filename)) hash.update(chunk);
  return hash.digest('hex');
}

export class PackageDelivery {
  constructor({ releases, root, grantSecret, clock = () => new Date() }) {
    this.releases = releases;
    this.root = path.resolve(root);
    this.grantSecret = grantSecret;
    this.clock = clock;
  }

  async authorize({ product, version, grant }) {
    const payload = verifyDownloadGrant(grant, this.grantSecret, this.clock().getTime());
    if (payload.product !== product || payload.version !== version) throw new Error('invalid_download_grant');
    const release = 'function' === typeof this.releases.findVersion
      ? this.releases.findVersion(product, version)
      : this.releases.find((item) => item.product === product && item.version === version);
    if (!release || !release.file) throw new Error('release_not_found');
    const candidate = path.resolve(this.root, release.file);
    const root = await realpath(this.root);
    const actual = await realpath(candidate);
    if (actual !== root && !actual.startsWith(`${root}${path.sep}`)) throw new Error('invalid_package_path');
    const info = await stat(actual);
    if (!info.isFile()) throw new Error('release_not_found');
    const digest = await sha256(actual);
    if (!/^[a-f0-9]{64}$/i.test(release.sha256) || digest.toLowerCase() !== release.sha256.toLowerCase()) throw new Error('package_corrupt');
    return { path: actual, size: info.size, filename: path.basename(release.file), sha256: digest };
  }
}
