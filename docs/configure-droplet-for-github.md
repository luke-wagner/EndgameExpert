# Setting up DigitalOcean Droplet for GitHub Development

## 1. Configure Git globally

Set your Git username and email globally (must match your GitHub account):
```bash
git config --global user.name "Your Name"
git config --global user.email "your_email@example.com"
```

Verify the configuration:
```bash
git config --global user.name
git config --global user.email
```

## 2. Generate SSH key (if none exists)

Check for existing SSH keys:
```bash
ls ~/.ssh
```

If no keys exist, generate a new one:
```bash
ssh-keygen -t ed25519 -C "your_email@example.com"
```

- Press Enter to accept the default file location
- Press Enter again for no passphrase (optional)

## 3. Add the SSH key to GitHub

Copy the public key:
```bash
cat ~/.ssh/id_ed25519.pub
```

1. Go to **GitHub → Settings → SSH and GPG keys → New SSH key**
2. Give it a title (e.g., "DigitalOcean droplet")
3. Paste the public key and save

## 4. Verify SSH connection

```bash
ssh -T git@github.com
```

- The first time, you may see:
  ```
  The authenticity of host 'github.com (140.82.113.4)' can't be established.
  ED25519 key fingerprint is SHA256:+DiYg2...
  Are you sure you want to continue connecting (yes/no)?
  ```
- Type `yes` to accept
- You should see:
  ```
  Hi yourusername! You've successfully authenticated.
  ```

## 5. Clone repository or change existing repository to use SSH

### If you haven't cloned the repository yet:
Clone using SSH from the start:
```bash
git clone git@github.com:yourusername/yourrepo.git
cd yourrepo
```

### If you already have a repository cloned with HTTPS:
Check your current remote URL:
```bash
git remote -v
```

If it starts with `https://`, switch to SSH:
```bash
git remote set-url origin git@github.com:yourusername/yourrepo.git
```

Verify:
```bash
git remote -v
```

You should now see `git@github.com:...`

## 6. Make commits and push

```bash
git add .
git commit -m "Your commit message"
git push origin main
```

- Replace `main` with your branch name if different
- You should not be prompted for a password

## Notes

- Each new droplet or machine needs its own SSH key added to GitHub
- If you encounter permission errors, ensure your SSH directory has correct permissions: `chmod 700 ~/.ssh` and `chmod 600 ~/.ssh/id_ed25519`
- Use `ssh-add -l` to verify your SSH agent has loaded the key if authentication fails