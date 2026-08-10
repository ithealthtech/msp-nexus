import { createServer } from 'node:http';
import { createReadStream } from 'node:fs';
import { realpath, stat } from 'node:fs/promises';
import path from 'node:path';

const root = await realpath(process.cwd());
const port = Number.parseInt(process.env.PREVIEW_PORT ?? '4173', 10);
const types = { '.html': 'text/html; charset=utf-8', '.css': 'text/css; charset=utf-8', '.js': 'text/javascript; charset=utf-8', '.png': 'image/png', '.webp': 'image/webp', '.svg': 'image/svg+xml' };

createServer(async (request, response) => {
  try {
    const url = new URL(request.url, 'http://local.test');
    const pathname = url.pathname === '/' || url.pathname === '/preview/' ? '/preview/index.html' : url.pathname;
    const candidate = path.resolve(root, `.${decodeURIComponent(pathname)}`);
    const actual = await realpath(candidate);
    if (!actual.startsWith(`${root}${path.sep}`)) throw new Error('outside_root');
    const info = await stat(actual);
    if (!info.isFile()) throw new Error('not_file');
    response.writeHead(200, { 'content-type': types[path.extname(actual)] ?? 'application/octet-stream', 'content-length': info.size, 'cache-control': 'no-store', 'x-content-type-options': 'nosniff' });
    createReadStream(actual).pipe(response);
  } catch (error) {
    response.writeHead(404, { 'content-type': 'text/plain; charset=utf-8' });
    response.end(`Not found: ${error instanceof Error ? error.message : 'unknown'}`);
  }
}).listen(port, '127.0.0.1', () => console.log(`Preview: http://127.0.0.1:${port}/preview/`));
