CREATE TABLE form_data(
   id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
   data           TEXT    NOT NULL,
   created_at     DATETIME NOT NULL,
   files          BINARY,
   ip             CHAR(64),
   user_agent     CHAR(256)
);

CREATE INDEX "id" ON "form_data" ("id");
