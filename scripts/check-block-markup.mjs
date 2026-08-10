import { readFile } from 'node:fs/promises';
import { execFileSync } from 'node:child_process';

const output = execFileSync('rg', ['--files', 'packages/msp-nexus/templates', 'packages/msp-nexus/parts', 'packages/msp-nexus/patterns'], { encoding: 'utf8' });
const files = output.split(/\r?\n/).filter(Boolean).filter((file) => /\.(?:html|php)$/i.test(file));
const errors = [];
for (const file of files) {
  const source = await readFile(file, 'utf8');
  const stack = [];
  const comments = source.matchAll(/<!--\s*(\/?)wp:([a-z0-9-]+(?:\/[a-z0-9-]+)?)([\s\S]*?)-->/gi);
  for (const match of comments) {
    const closing = match[1] === '/';
    const name = match[2];
    const selfClosing = !closing && match[3].trimEnd().endsWith('/');
    if (selfClosing) continue;
    if (!closing) stack.push(name);
    else if (stack.pop() !== name) errors.push(`${file}: mismatched closing block ${name}`);
  }
  if (stack.length) errors.push(`${file}: unclosed blocks ${stack.join(', ')}`);
}
if (errors.length) {
  console.error(errors.join('\n'));
  process.exitCode = 1;
} else {
  console.log(`Block markup check passed (${files.length} files).`);
}
