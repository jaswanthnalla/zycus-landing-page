// Real device-emulated screenshots via puppeteer-core (uses your installed Chrome).
import puppeteer from 'puppeteer-core';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const URL = 'https://zycus-landing-preview.vercel.app';
const CHROME = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

const shots = [
  { name: 'desktop-1440x900.png',    viewport: { width: 1440, height: 900,  deviceScaleFactor: 1, isMobile: false }, fullPage: false },
  { name: 'desktop-fullpage.png',    viewport: { width: 1440, height: 900,  deviceScaleFactor: 1, isMobile: false }, fullPage: true  },
  { name: 'tablet-768x1024.png',     viewport: { width: 768,  height: 1024, deviceScaleFactor: 2, isMobile: true,  hasTouch: true }, fullPage: false },
  { name: 'mobile-375x812.png',      viewport: { width: 375,  height: 812,  deviceScaleFactor: 3, isMobile: true,  hasTouch: true }, fullPage: false },
  { name: 'mobile-fullpage.png',     viewport: { width: 375,  height: 812,  deviceScaleFactor: 3, isMobile: true,  hasTouch: true }, fullPage: true  },
];

const browser = await puppeteer.launch({
  executablePath: CHROME,
  headless: 'new',
  args: ['--no-sandbox', '--disable-gpu'],
});

for (const shot of shots) {
  const page = await browser.newPage();
  await page.setViewport(shot.viewport);
  if (shot.viewport.isMobile) {
    await page.setUserAgent('Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1');
  }
  console.log(`  capturing ${shot.name} (${shot.viewport.width}x${shot.viewport.height}${shot.fullPage ? ' fullpage' : ''}) ...`);
  await page.goto(URL, { waitUntil: 'networkidle0', timeout: 60000 });
  await new Promise(r => setTimeout(r, 1500)); // let fonts + counter settle
  const out = path.join(__dirname, shot.name);
  await page.screenshot({ path: out, fullPage: shot.fullPage, type: 'png' });
  await page.close();
}

await browser.close();
console.log('Done.');
