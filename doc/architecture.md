# Architecture
> SOA + Domain 기반 + MVC & Humble Pattern

```mermaid
---
title: Pyoniverse Dashboard Architecture
---
C4Component
    title Pyoniverse Dashboard Architecture
    Person(admin, "Pyoniverse Manager")
    Component(gateway, "Router", "")
    BiRel(admin, gateway, "Request/Response")
    UpdateRelStyle(admin, gateway, $offsetX="-50", $offsetY="-10")

    Container_Boundary(web_boundary, "Dashboard Web Application") {
        Component(middleware, "Middleware", "", "Security & Log")
        BiRel(gateway, middleware, "Verify/Log")

        Container_Boundary(product_domain, "Products") {
            Component(product_controller, "Controller")
            Component(product_business, "Business")
            Component(product_entity, "ProductEntity")

            BiRel(middleware, product_controller, "handle")
            BiRel(product_controller, product_business, "process")
            BiRel(product_controller, view, "Render")
            BiRel(product_business, product_entity, "use")
            BiRel(product_business, brand_entity, "use")
            UpdateRelStyle(product_controller, view, $textColor="blue")
            UpdateRelStyle(product_controller, product_business, $textColor="green", $lineColor="red")
            UpdateRelStyle(product_business, product_entity, $textColor="green", $lineColor="red")
            UpdateRelStyle(product_business, brand_entity, $textColor="blue")
        }

        Container_Boundary(common, "Common") {
            Component(view, "View", "", "Provides Dashboard UI")
            Component(brand_entity, "BrandEntity")
            ComponentDb(rdb, "RDB", "MariaDB", "Dashboard DB")
            BiRel(brand_entity, rdb, "persist")
            BiRel(product_entity, rdb, "persist")
            UpdateRelStyle(brand_entity, rdb, $textColor="pupple", $lineColor="blue")
            UpdateRelStyle(product_entity, rdb, $textColor="pupple", $lineColor="blue")
        }
    }
```
## 아키텍처 고려사항
### 아키텍처 특성
**보안성**
프로젝트 관리자만 데이터를 수정할 수 있어야 한다.

**사용성**
서비스 사용방법을 익히는데 적은 시간이 들어야 한다.

**신뢰성**
수정 결과는 운영 환경에 반영되고, 이후 데이터가 덮어씌워지지 않는다.

### 아키텍처 결정
- 변경된 데이터는 일단위로 반영된다.
- 서비스는 Full-stack 개발자 1인에 의해 개발된다.

### 설계원칙
- 테스트 코드부터 작성해야 한다.
- Humble 패턴을 사용해야 한다.
- commit 전에 코드 정리 및 테스트케이스를 통과해야 한다.
- 배포는 자동으로 이루어져야 한다.
