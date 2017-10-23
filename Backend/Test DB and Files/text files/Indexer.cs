﻿using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading;
using System.Data.SQLite;
using System.Data.SQLite.Linq;
using System.IO;

namespace Crawler
{
	public class Indexer
	{
		private Indexer() { }
		static Indexer() { }
		private static Indexer _instance = new Indexer();
		public static Indexer Instance { get { return _instance; } }

		SQLiteConnection db = null;
		readonly string dbFileName = "file_indices.sqlite";

		Thread dbThread;
		enum ThreadStatus { IDLE, RUNNING };
		ThreadStatus threadStatus = ThreadStatus.IDLE;

		Queue<string> commandQueue = new Queue<string>();
		List<string> words = new List<string>();
		List<Tuple<string,string>> links = new List<Tuple<string, string>>();

		public void Init()
		{
			bool doesDBExists = File.Exists(dbFileName);

			// create new .sqlite file
			if (!doesDBExists) SQLiteConnection.CreateFile(dbFileName);

			db = new SQLiteConnection("Data Source=" + dbFileName + "; Version=3");
			db.Open();
			using (SQLiteCommand command = new SQLiteCommand(db))
			{
				try
				{
					// check if our table exists
					command.CommandText =
						"SELECT 1 FROM files LIMIT 1;" +
						"SELECT 1 FROM words LIMIT 1;" +
						"SELECT 1 FROM links LIMIT 1;";
					command.ExecuteNonQuery();
				}
				catch (Exception e)
				{
					// table doesn't exist, create new table
					command.CommandText =
						"CREATE TABLE files (id TEXT UNIQUE, file_path TEXT UNIQUE);\n" +
						"CREATE TABLE words (id TEXT UNIQUE, word TEXT UNIQUE);\n" +
						"CREATE TABLE links (word_id TEXT, file_id TEXT);";
					command.ExecuteNonQuery();
				}
			}

			//words = GetAllWords();
			//links = GetAllWordLinks();

			dbThread = new Thread(Update);
			dbThread.Start();
		}

		void Update()
		{
			DateTime start = DateTime.Now;

			do
			{
				if (commandQueue.Count > 10 || (DateTime.Now - start).TotalMilliseconds > 100)
				{
					if (commandQueue.Count > 0)
					{
						threadStatus = ThreadStatus.RUNNING;
						using (SQLiteCommand command = new SQLiteCommand(db))
						{
							using (SQLiteTransaction transaction = db.BeginTransaction())
							{
								try
								{
									int queueCount = commandQueue.Count; // prevent from adding commands that are still coming in from main thread
									for (int i = 0; i < queueCount && i < 100; ++i)
									{
										command.CommandText += commandQueue.Dequeue() + ";\n"; // must have semicolon
									}
									command.ExecuteNonQueryAsync();
									Console.WriteLine(queueCount);
								}
								catch (Exception e)
								{
									Console.WriteLine(e.Message);
								}

								transaction.Commit();
							}
						}
						threadStatus = ThreadStatus.IDLE;
					}

					start = DateTime.Now;
				}
			}
			while (true);
		}

		public void WaitToEnd()
		{
			while (commandQueue.Count > 0 || threadStatus == ThreadStatus.RUNNING) { }
			dbThread.Abort();
		}

		public void AddFileName(string path)
		{
			if (path == "") return;

			using (SQLiteCommand command = new SQLiteCommand(db))
			{
				commandQueue.Enqueue("INSERT OR REPLACE INTO files (id, file_path) VALUES (HEX(RANDOMBLOB(16)), \"" + path + "\")");
			}
		}

		public void AddWordEntry(string word)
		{
			if (word == "") return;

			using (SQLiteCommand command = new SQLiteCommand(db))
			{
				commandQueue.Enqueue("INSERT OR REPLACE INTO words (id, word) VALUES (HEX(RANDOMBLOB(16)), \"" + word + "\")");
			}
		}

		public void AddWordLink(string word, string fullPath)
		{
			if (word == null || fullPath == null ||
				word == "" || fullPath == "") return;

			using (SQLiteCommand command = new SQLiteCommand(db))
			{
				commandQueue.Enqueue("INSERT INTO links (word_id, file_id) SELECT " +
				"(SELECT words.id FROM words WHERE \"" + word + "\" = words.word), " +
				"(SELECT files.id FROM files WHERE \"" + fullPath + "\" = files.file_path)" +
				"WHERE EXISTS (" +
				"SELECT words.id FROM words WHERE \"" + word + "\" = words.word)");
			}
		}

		public List<string> GetAllFileNames()
		{
			List<string> filenames = new List<string>();

			using (SQLiteCommand command = new SQLiteCommand(db))
			{
				command.CommandText = "SELECT * FROM files ORDER BY name DESC";
				using (SQLiteDataReader reader = command.ExecuteReader())
				{
					while (reader.Read()) filenames.Add(reader["name"].ToString());
				}
			}

			return filenames;
		}

		public List<string> GetAllWords()
		{
			List<string> words = new List<string>();

			using (SQLiteCommand command = new SQLiteCommand(db))
			{
				command.CommandText = "SELECT * FROM words ORDER BY word DESC";
				using (SQLiteDataReader reader = command.ExecuteReader())
				{
					while (reader.Read()) words.Add(reader["word"].ToString());
				}
			}

			return words;
		}

		/// heavy function, call with caution!
		/// T1 is the word, T2 is the file path
		public List<Tuple<string, string>> GetAllWordLinks()
		{
			List<Tuple<string, string>> links = new List<Tuple<string, string>>();

			using (SQLiteCommand command = new SQLiteCommand(db))
			{
				command.CommandText =
				"SELECT words.word AS word, files.file_path AS file_path\n" +
				"FROM ((links\n" +
				"INNER JOIN words ON links.word_id = words.id)\n" +
				"INNER JOIN files ON links.file_id = files.id)\n" +
				"ORDER By words.word DESC";
				using (SQLiteDataReader reader = command.ExecuteReader())
				{
					while (reader.Read()) links.Add(new Tuple<string, string>(reader["word"].ToString(), reader["file_path"].ToString()));
				}
			}

			return links;
		}
	}
}