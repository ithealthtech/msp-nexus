import { readFile } from 'node:fs/promises';
import { execFileSync } from 'node:child_process';

const output = execFileSync('rg', ['--files', 'packages', 'services', 'scripts', 'docs'], { encoding: 'utf8' });
const files = output.split(/\r?\n/).filter(Boolean).filter((file) => !/\.(png|zip|gz|pot)$/i.test(file));
const forbidden = [
  /-----BEGIN (?:RSA |EC |OPENSSH )?PRIVATE KEY-----/,
  /(?:api[_-]?key|client[_-]?secret|access[_-]?token)\s*[:=]\s*["'][A-Za-z0-9_\-]{20,}["']/i,
  /sk-(?:live|prod)-[A-Za-z0-9]{16,}/i
];
const findings = [];
for (const file of files) {
  const text = await readFile(file, 'utf8');
  for (const pattern of forbidden) if (pattern.test(text)) findings.push(`${file}: ${pattern}`);
}
if (findings.length) {
  console.error('Potential committed secrets detected:\n' + findings.join('\n'));
  process.exitCode = 1;
} else {
  console.log(`Secret scan passed (${files.length} text files).`);
}
