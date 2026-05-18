"""Capture desktop + mobile screenshots of the live deployed landing page."""
import os, subprocess, time

CHROME = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
URL = "https://zycus-landing-preview.vercel.app"
OUT = os.path.dirname(os.path.abspath(__file__))

shots = [
    # (filename, viewport_w, viewport_h)
    ("desktop-1440x900.png", 1440, 900),
    ("desktop-fullpage.png", 1440, 3400),
    ("tablet-768x1024.png", 768, 1024),
    ("mobile-375x812.png", 375, 812),
    ("mobile-fullpage.png", 375, 3600),
]

for fname, w, h in shots:
    out = os.path.join(OUT, fname)
    if os.path.exists(out):
        os.remove(out)
    args = [
        CHROME,
        "--headless=new",
        "--disable-gpu",
        "--hide-scrollbars",
        "--no-sandbox",
        f"--window-size={w},{h}",
        f"--screenshot={out}",
        "--virtual-time-budget=4000",
        URL,
    ]
    print(f"  capturing {fname} ({w}x{h}) ...", flush=True)
    r = subprocess.run(args, capture_output=True, timeout=60)
    if r.returncode != 0:
        print(f"    stderr: {r.stderr.decode('utf-8','ignore')[:200]}")
    size = os.path.getsize(out) if os.path.exists(out) else 0
    print(f"    -> {size:,} bytes")

print("Done.")
