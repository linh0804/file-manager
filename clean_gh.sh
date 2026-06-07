# Xoá tất cả releases
gh release list --limit 1000 --json tagName -q '.[].tagName' |
while read -r tag; do
    gh release delete "$tag" --yes
done

# Xoá tất cả tags local và remote
git tag -l |
while read -r tag; do
    git tag -d "$tag" 2>/dev/null
    git push origin ":refs/tags/$tag"
done

# Xoá tất cả workflow runs
gh run list --limit 1000 --json databaseId -q '.[].databaseId' |
while read -r run_id; do
    gh run delete "$run_id"
done


