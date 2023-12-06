# ku-database
> 고려대학교 2023-2학기 데이터베이스 프로젝트
## 개요
편의점 5개사(CU, Seven Eleven, Emart24, CSpace, GS25)의 웹 및 앱사이트에서 수집한 데이터를 자사 서비스(Pyoniverse)에서 사용할 수 있도록 변환 및 정제하는 과정에서 특정 속성의 값(category 등)이 부족하거나 잘못 분류된 데이터가 지속적으로 존재한다. 이러한 데이터를 손쉽게 확인하고 정제하기 위해 Dashboard 서비스가 필요하다.
## 사용자 스토리
1. Public Web 상에서 접근 가능한 Dashboard
2. 프로젝트 관계자만 접근할 수 있어야 한다.
3. 확인하고 싶은 속성에 대해 필터를 걸 수 있어야 한다.
4. 대시보드에서 데이터를 수정할 수 있어야 한다.
5. 수정된 데이터는 ETL Pipeline의 데이터 업데이트에서 덮어씌워지지 않아야 한다.
6. 데이터의 업데이트는 즉각 반영되지 않아도 된다.
## 요구사항
1. https://dashboard.pyoniverse.kr 를 통해 접근할 수 있어야 한다.
2. 관리자 로그인이 필요하다(관리자는 1명이다)
3. 속성 및 값에 대한 필터링이 필요하다.
4. 대시보드의 데이터 수정 결과는 운영 DB에 반영되고, 다른 방식의 데이터 업데이트 결과로 덮어씌워지지 않는다.
5. 데이터 업데이트는 하루 한번으로 제한한다.
6. 대시보드에서 업데이트된 속성을 확인할 수 있어야 한다.
## 데이터 설계
- [Entity Relationship Diagram](doc/erd.md)
## 기능 설계
- [Architecture](doc/architecture.md)
- [Module Design](doc/module.md)
## 구현
- [SQL Schema](database/schema.sql)
- [Code Summary](doc/implementation.md)
