
#include "stratum.h"

// sql injection security, unwanted chars
void db_check_user_input(char* input)
{
	char *p = NULL;
	if (input && input[0]) {
		p = strpbrk(input, "\"'\\");
		if(p) *p = '\0';
	}
}

void db_add_user(YAAMP_DB *db, YAAMP_CLIENT *client)
{
	db_clean_string(db, client->username);
	db_clean_string(db, client->password);
	db_clean_string(db, client->version);
	db_clean_string(db, client->notify_id);
	db_clean_string(db, client->worker);

	char symbol[16] = { 0 };
	char *p = strstr(client->password, "c=");
	if(!p) p = strstr(client->password, "s=");
	if(p) strncpy(symbol, p+2, 15);
	p = strchr(symbol, ',');
	if(p) *p = 0;

	db_check_user_input(client->username);

//	debuglog("user %s %s\n", client->username, symbol);
	db_query(db, "SELECT id, is_locked, logtraffic, coinid FROM accounts WHERE username='%s'", client->username);

	MYSQL_RES *result = mysql_store_result(&db->mysql);
	if(!result) return;

	MYSQL_ROW row = mysql_fetch_row(result);
	if(row)
	{
		if(row[1] && atoi(row[1])) client->userid = -1;
		else client->userid = atoi(row[0]);

		client->logtraffic = row[2] && atoi(row[2]);
		client->coinid = row[3] ? atoi(row[3]) : 0;
	}

	mysql_free_result(result);

	db_check_user_input(symbol);

	if(client->userid == -1)
		return;

	else if(client->userid == 0)
	{
		db_query(db, "INSERT INTO accounts (username, coinsymbol, balance) values ('%s', '%s', 0)", client->username, symbol);
		client->userid = (int)mysql_insert_id(&db->mysql);
	}

	else
		db_query(db, "UPDATE accounts SET coinsymbol='%s' WHERE id=%d", symbol, client->userid);
}

//////////////////////////////////////////////////////////////////////////////////////

void db_clear_worker(YAAMP_DB *db, YAAMP_CLIENT *client)
{
	if(!client->workerid)
		return;

	db_query(db, "DELETE FROM workers WHERE id=%d", client->workerid);
	client->workerid = 0;
}

void db_add_worker(YAAMP_DB *db, YAAMP_CLIENT *client)
{
	db_clear_worker(db, client);
	int now = time(NULL);

	/* maybe not required here (already made), but... */
	db_check_user_input(client->username);
	db_check_user_input(client->version);
	db_check_user_input(client->password);
	db_check_user_input(client->worker);

	db_query(db, "INSERT INTO workers (userid, ip, name, difficulty, version, password, worker, algo, time, pid) "\
		"VALUES (%d, '%s', '%s', %f, '%s', '%s', '%s', '%s', %d, %d)",
		client->userid, client->sock->ip, client->username, client->difficulty_actual,
		client->version, client->password, client->worker, g_stratum_algo, now, getpid());

	client->workerid = (int)mysql_insert_id(&db->mysql);
}

void db_update_workers(YAAMP_DB *db)
{
	g_list_client.Enter();
	for(CLI li = g_list_client.first; li; li = li->next)
	{
		YAAMP_CLIENT *client = (YAAMP_CLIENT *)li->data;
		if(client->deleted) continue;
		if(!client->workerid) continue;

		if(client->speed < 0.00001)
		{
			clientlog(client, "speed %f", client->speed);
			shutdown(client->sock->sock, SHUT_RDWR);

			continue;
		}

		client->speed *= 0.8;
		if(client->difficulty_written == client->difficulty_actual) continue;

		db_query(db, "UPDATE workers SET difficulty=%f, subscribe=%d WHERE id=%d",
			client->difficulty_actual, client->extranonce_subscribe, client->workerid);
		client->difficulty_written = client->difficulty_actual;
	}

	client_sort();
	g_list_client.Leave();
}

void db_init_user_coinid(YAAMP_DB *db, YAAMP_CLIENT *client)
{
	if (!client->userid)
		return;

	if (!client->coinid)
		db_query(db, "UPDATE accounts SET coinid=NULL WHERE id=%d", client->userid);
	else
		db_query(db, "UPDATE accounts SET coinid=%d WHERE id=%d AND IFNULL(coinid,0) = 0",
			client->coinid, client->userid);
}

