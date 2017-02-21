using System.Collections;
using System.Collections.Generic;
using UnityEngine;

public class TestScript : MonoBehaviour {

	// Use this for initialization
	void Start () {
		Debug.Log ("test");
		var osn = new OpenSimplexNoise(2);
		Debug.Log(osn.Evaluate(1,1));
	}
	
	// Update is called once per frame
	void Update () {
		
	}
}
