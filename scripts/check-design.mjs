function rgb(hex) {
  const value = hex.replace('#', '');
  return [0, 2, 4].map((offset) => Number.parseInt(value.slice(offset, offset + 2), 16) / 255);
}
function luminance(hex) {
  const values = rgb(hex).map((value) => value <= 0.04045 ? value / 12.92 : ((value + 0.055) / 1.055) ** 2.4);
  return values[0] * 0.2126 + values[1] * 0.7152 + values[2] * 0.0722;
}
function contrast(a, b) {
  const [light, dark] = [luminance(a), luminance(b)].sort((x, y) => y - x);
  return (light + 0.05) / (dark + 0.05);
}

const pairs = [
  ['Base body', '#ffffff', '#0b0a17', 4.5],
  ['Primary button', '#0b0a17', '#69bff5', 4.5],
  ['Body on navy', '#d9d9e5', '#121124', 4.5],
  ['Dark text link', '#216b99', '#ffffff', 4.5],
  ['Cyan link on ink', '#69bff5', '#0b0a17', 4.5],
  ['Secondary label on ink', '#8b91aa', '#0b0a17', 4.5],
  ['Cyber action', '#07110d', '#60f0a8', 4.5],
  ['Corporate action', '#ffffff', '#294260', 4.5],
  ['Cloud body', '#16132b', '#f8f6ff', 4.5]
];
let failed = false;
for (const [label, foreground, background, minimum] of pairs) {
  const ratio = contrast(foreground, background);
  console.log(`${label}: ${ratio.toFixed(2)}:1`);
  if (ratio < minimum) failed = true;
}
if (failed) process.exitCode = 1;
