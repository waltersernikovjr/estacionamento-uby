# Development Guidelines

## 🚨 Critical Rules

### Always Use Migrations
```bash
# ✅ Correct
docker-compose exec backend php artisan make:migration add_column_to_table

# ❌ Wrong - Never modify database directly
```

### Keep Code Clean
- **No unnecessary comments** in production code
- **Write in English**: commits, code, documentation
- **Remove debug code**: dd(), var_dump(), console.log()

### Git Workflow
```bash
# Commit messages in English
git commit -m "feat(module): add new feature"
git commit -m "fix(module): correct bug"
git commit -m "docs(module): update documentation"
```

## 📝 Adding/Modifying API Endpoints

### When creating a new endpoint:

1. **Create Migration** (if needed)
2. **Update Model** (fillable, casts)
3. **Update Repository** (interface + implementation)
4. **Update Service** (business logic)
5. **Create/Update Controller**
6. **⚠️ DOCUMENT IN SWAGGER** → `app/Http/Controllers/Api/ApiDocumentation.php`
7. **Regenerate docs**: `docker-compose exec backend php artisan l5-swagger:generate`
8. **Test in Swagger UI**: http://localhost:8000/api/documentation

### When modifying an existing endpoint:

1. **Update code** (Model, Repository, Service, Controller)
2. **⚠️ UPDATE SWAGGER DOCUMENTATION** → `ApiDocumentation.php`
3. **Regenerate docs**: `docker-compose exec backend php artisan l5-swagger:generate`
4. **Test in Swagger UI**

## 📚 Swagger Documentation Rules

### ✅ DO:
- Document **ALL** endpoints in `ApiDocumentation.php`
- Include request/response examples
- Update documentation **immediately** after code changes
- Regenerate docs before committing

### ❌ DON'T:
- Add `@OA\*` annotations in business logic controllers
- Mix documentation with business logic
- Forget to regenerate documentation
- Commit without testing in Swagger UI

## 🔄 Development Workflow

```bash
# 1. Start containers
docker-compose up -d

# 2. Create feature branch
git checkout -b feature/your-feature

# 3. Make changes...
# - Migrations
# - Models
# - Repositories
# - Services
# - Controllers
# - ⚠️ SWAGGER DOCUMENTATION

# 4. Regenerate Swagger
docker-compose exec backend php artisan l5-swagger:generate

# 5. Test
docker-compose exec backend php artisan test
# Test manually in Swagger UI

# 6. Commit (in English, no unnecessary comments)
git add .
git commit -m "feat(module): add new feature"

# 7. Push
git push origin feature/your-feature
```

## 🧪 Testing Checklist

Before committing:
- [ ] Migrations run successfully
- [ ] Unit tests pass: `docker-compose exec backend php artisan test`
- [ ] Swagger documentation updated
- [ ] Swagger UI working: http://localhost:8000/api/documentation
- [ ] All endpoints tested manually
- [ ] No debug code left (dd(), var_dump(), etc)
- [ ] No unnecessary comments
- [ ] Code in English
- [ ] Commit messages in English

## 📋 Swagger Example

```php
// app/Http/Controllers/Api/ApiDocumentation.php

/**
 * @OA\Post(
 *     path="/api/v1/your-endpoint",
 *     summary="Brief description",
 *     operationId="operationId",
 *     tags={"TagName"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"field1"},
 *             @OA\Property(property="field1", type="string", example="value")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object")
 *         )
 *     )
 * )
 */
public function docYourEndpoint() {}
```

## 🚀 Quick Commands

```bash
# Clear cache
docker-compose exec backend php artisan config:clear
docker-compose exec backend php artisan cache:clear

# Run migrations
docker-compose exec backend php artisan migrate:fresh --seed

# Run tests
docker-compose exec backend php artisan test

# Regenerate Swagger
docker-compose exec backend php artisan l5-swagger:generate
```

## 📖 Documentation Location

- **API Documentation**: http://localhost:8000/api/documentation
- **Swagger Annotations**: `app/Http/Controllers/Api/ApiDocumentation.php`
- **API Guide**: `backend/README_API.md`
- **This Guide**: `DEVELOPMENT.md`

---

**Remember: If it's not documented in Swagger, it doesn't exist!**
