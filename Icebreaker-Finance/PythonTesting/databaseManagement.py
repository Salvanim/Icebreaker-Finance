import mysql.connector
import switchObject

class DatabaseManager():
    def __init__(self, host, port, user, password, database):
        self.host = host
        self.port = port
        self.user = user
        self.password = password
        self.database = database

        self.db_connection = mysql.connector.connect(
            host=host,
            port=port,
            user=user,
            passwd=password,
            database=database
        )
        self.cursor = self.db_connection.cursor()

    def execute(self, *command, multiline=True, fetchType="all", size=1, multi=False):
        output = []
        if multiline:
            finalCommandString = "\n".join(command)
            self.cursor.execute(finalCommandString, multi=multi)
            output.append(self.fetch(fetchType, size))
        else:
            for com in command:
                self.cursor.execute(com, multi=multi)
                output.append(self.fetch(fetchType, size))
        return output

    def viewColumnEqual(self, tableName, columnName, columnData):
        columnDataString = f"'{columnData}'" if isinstance(columnData, str) else str(columnData)
        return self.execute(f"SELECT * FROM {self.database}.{tableName} WHERE {columnName} = {columnDataString};")

    def update(self, tableName, columns=[], change=[], values=[], conditions=[]):
        values = values[:len(columns)] + ["null"] * (len(columns) - len(values))
        change = change[:len(columns)] + ['='] * (len(columns) - len(change))

        updateStrings = [f"{columns[i]} {change[i]} {values[i]}" for i in range(len(columns))]
        executeString = f"UPDATE {tableName} SET {', '.join(updateStrings)}"

        if conditions:
            conditionalString = " WHERE " + " OR ".join(conditions)
        else:
            conditionalString = ""

        self.execute(executeString + conditionalString + ";")
        self.commit()

    def insert(self, tableName, columns=[], values=[]):
        values = values[:len(columns)] + ["null"] * (len(columns) - len(values))

        colString = ", ".join(columns)
        valString = ", ".join(f"'{v}'" if isinstance(v, str) else str(v) for v in values)
        executeString = f"INSERT INTO {self.database}.{tableName} ({colString}) VALUES ({valString});"

        output = self.execute(executeString)
        self.commit()
        return output

    def viewTable(self, tableName, *columns):
        columnSelection = ", ".join(columns) if columns else "*"
        output = self.execute(f"SELECT {columnSelection} FROM {self.database}.{tableName};")
        self.commit()
        return output

    def info(self):
        self.cursor.execute(f"""SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '{self.database}';""")
        tables = [row[0] for row in self.cursor.fetchall()]

        tableColumnNames = {}
        for table in tables:
            self.cursor.execute(f"""SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{self.database}' AND TABLE_NAME = '{table}';""")
            tableColumnNames[table] = [row[0] for row in self.cursor.fetchall()]
        self.commit()
        return tableColumnNames

    def fetch(self, selection, size=1):
        selection = selection.lower()
        try:
            case = switchObject.switch(
                "all", self.cursor.fetchall,
                "many", self.cursor.fetchmany,
                "one", self.cursor.fetchone,
                "warn", self.cursor.fetchwarnings,
                end=self.cursor.fetchall
            )
            return case(selection, size)
        except:
            return ""

    def close(self):
        self.cursor.close()
        self.db_connection.close()

    def open(self):
        self.db_connection = mysql.connector.connect(
            host=self.host,
            port=self.port,
            user=self.user,
            passwd=self.password,
            database=self.database
        )
        self.cursor = self.db_connection.cursor()

    def getTableNames(self):
        return list(self.info().keys())

    def clearTable(self, index, resetPrimary=True):
        tableName = self.getTableNames()[index]
        self.execute(f"DELETE FROM {self.database}.{tableName};")
        self.commit()
        if resetPrimary:
            self.resetPrimary(tableName, 1)
    
    def primaryKeys(self, tableName):
        output = self.execute(f"""SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
                                  WHERE TABLE_SCHEMA = '{self.database}'
                                  AND TABLE_NAME = '{tableName}'
                                  AND COLUMN_KEY = 'PRI';""")
        self.commit()
        return output[0][0][0] if output and output[0] else None

    def resetPrimary(self, tableName, startValue):
        primaryName = self.primaryKeys(tableName)
        if primaryName:
            self.execute(f"SET @new_id = {startValue - 1};")
            self.execute(f"UPDATE {tableName} SET {primaryName} = (@new_id := @new_id + 1) ORDER BY {primaryName};")
            self.execute(f"ALTER TABLE {tableName} AUTO_INCREMENT = {startValue};")
            self.commit()

    def commit(self):
        self.db_connection.commit()
