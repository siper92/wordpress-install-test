# Commands to build images

# wp image
```bash
docker buildx build -f wp/Dockerfile -t task_atop:wp.dev .
docker tag task:wp.dev task:wp.dev
```

# Server image
```bash
docker buildx build -f server/Dockerfile -t task_atop:server .
docker tag task:server task:server
```