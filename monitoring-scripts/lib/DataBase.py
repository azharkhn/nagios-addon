#!/usr/bin/python

import MySQLdb

class MySQL:
    '''
        This is wrapper Class of MySQLdb package
    '''

    connection = ''
    cursor = ''
    insertid = 0

    def connect(self, dbhost, dbuser, dbpass, dbname, port=3306):
        '''
        (string , string, string, string, integer) => None

        This is constructor function and it makes a connection to mentioned database.

        >>> connect('Hostname','username','password','database',3308)
        >>> connect('Hostname','username','password','database')

        Note: This function is only for INSERT & Update queries
        '''
        try:
            self.connection = MySQLdb.connect(dbhost, dbuser, dbpass, dbname, port)
            self.cursor = self.connection.cursor()
            return True
        
        except MySQLdb.Error, err:
            error = str(err[0])+': '+err[1]
            return error

    def modify(self, query, rows=None):
        '''
        (string , integer) => string

        This function executes (INSERT & UPDATE only) SQL queries and if there is some error then returns the Error.

        >>> object.modify('UPDATE tablename set column = 2;)
        >>> object.modify('INSERT INTO tablename (`column`) VALUES('2')')

        '''
        try:
            if rows == None:
                self.cursor.execute(query)
                self.insertid = self.connection.insert_id()
            else:
                self.cursor.executemany(query)
            
            self.connection.commit()
            return True
        
        except MySQLdb.Error, err:
            self.connection.rollback()
            error = str(err[0])+': '+err[1] 
            return error
 
    def fetchrows(self, query, rows=None):
        '''
        (string , integer) => tuple

        This function executes SELECT SQL queries and return query results in tuple .

        >>> print object.fetchrows('SELECT * from tablename', 2)
            ((1L,1,2),(2L,3,4))
        >>> print object.fetchrows('SELECT column1, column2 from tablename')
            ((1L,1,2),(2L,3,4),(3L,5,6))

        '''
        try:
            self.cursor.execute(query)
            if rows == None:
                return self.cursor.fetchall()
            elif rows == 1:
                return self.cursor.fetchone()
            else:
                return self.cursor.fetchmany(rows)
        
        except MySQLdb.Error, err:
            error = str(err[0])+': '+err[1] 
            return error

    def close(self):
        '''
        () => None

        This function only closes the Database connection.

        >>> object.close()

        '''

        self.connection.close()
